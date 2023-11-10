<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class PostTouiteAction extends Action
{

    private function ensureUserIsLoggedIn()
    {
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: ?action=signin');
            exit;
        }
    }

    public function execute(): string
    {
        $this->ensureUserIsLoggedIn();
        $post_touite_html = "";

        if ($this->http_method == "GET") {
            $post_touite_html .= <<<HTML
                  <header> 
                    <p class ='libelle_page_courante'>Publier un nouveau Touite</p>
                    <nav class="menu-nav">
                      <ul>
                        <li><a href="?action=default"><i class="fa fa-home"></i></a></li>
                      </ul>
                    </nav>
                  </header>
                  <div class="form-container">
                    <form class="form" action='?action=post-touite' method='post' enctype="multipart/form-data">
                        <h1> Publier votre Touite </h1>
                        <textarea placeholder='Votre touite ici' name='texte' id='texte' required></textarea><br><br>
                        <label for="media" class="custom-file-upload">
                          <i class="fa fa-cloud-upload"></i> Choisir un fichier
                        </label>
                        <input type='file' name='media' id='media' style="display: none;"><br><br>
                        <input type='submit' value='Publier'>
                    </form>
                  </div>
                  HTML;
        } else if ($this->http_method == "POST") {
            $connection = ConnectionFactory::makeConnection();
            // si l'utilisateur n'est pas connecté, alors on le redirige vers la page pour se connecter
            if (!isset($_SESSION['utilisateur'])) {
                header('Location: ?action=signin');
                exit;
            }

            // ID de l'utilisateur de la session
            $utilisateurID = $_SESSION['utilisateur']['userID'];

            // Valeurs du touite
            $texte = filter_input(INPUT_POST, 'texte', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = $_POST['texte'];
            $datePublication = date('Y-m-d H:i:s'); // Date et heure courantes

            /*********************************************
             *                                           *
             *      INSERTION DES DONNEES DANS LA BD     *
             *                                           *
             *********************************************/
            // insertion dans la table touites
            $query_touite = $connection->prepare("INSERT INTO touites (texte, datePublication) VALUES (:texte, :datePublication)");
            $query_touite->bindParam(':texte', $texte);
            $query_touite->bindParam(':datePublication', $datePublication);

            // Récupère l'id du touit et de l'image pour les insérer dans la table touitesimages et touitesutilisateur
            $query_recup_idTouite = $connection->prepare('SELECT max(touiteID) + 1 as touiteID FROM touites');
            $query_recup_idTouite->execute();
            $touiteID = $query_recup_idTouite->fetch()['touiteID'];

            // insertion dans la table touitesutilisateurs
            $query_touite_utilisateur = $connection->prepare("INSERT INTO touitesutilisateurs (touiteID, utilisateurID) VALUES (:touiteID, :utilisateurID)");
            $query_touite_utilisateur->bindParam(':touiteID', $touiteID);
            $query_touite_utilisateur->bindParam(':utilisateurID', $utilisateurID);

            try {
                $query_touite->execute();
                $query_touite_utilisateur->execute();
            } catch (\PDOException $e) {
                // Gérer l'erreur
                $post_touite_html .= "<p>Erreur lors de la publication du touite.</p>";
            }
            $mediaPath = null;
            $track_name = $_FILES['media']['name'];
            // Gérer le téléchargement du fichier (si un fichier est ajouté et vérifie son extension)
            if (isset($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
                if(substr($track_name, -4) === '.jpg' || substr($track_name, -5) === '.jpeg' || substr($track_name, -4) === '.png') {
                    // fichier uploader
                    $mediaPath = basename($track_name);
                    move_uploaded_file($_FILES['media']['tmp_name'], 'images/' . $mediaPath);

                    // insertion dans la table images
                    $query_image = $connection->prepare("INSERT INTO images (description, cheminFichier) VALUES (:description, :mediaPath)");
                    $query_image->bindParam(':description', $description);
                    $query_image->bindParam(':mediaPath', $mediaPath);

                    $query_recup_idImage = $connection->prepare('SELECT max(imageID) + 1 as imageID FROM images');
                    $query_recup_idImage->execute();
                    $imageID = $query_recup_idImage->fetch()['imageID'];

                    // insertion dans la table touitesimages
                    $query_touite_image = $connection->prepare("INSERT INTO touitesimages (touiteID, imageID) VALUES (:touiteID, :imageID)");
                    $query_touite_image->bindParam(":touiteID", $touiteID);
                    $query_touite_image->bindParam(":imageID", $imageID);

                    try {
                        $query_image->execute();
                        $query_touite_image->execute();
                    } catch (\PDOException $e) {
                        // Gérer l'erreur
                        $post_touite_html .= "<p>Erreur lors de la publication du touite.</p>";
                    }
                } else {
                    $post_touite_html .= "<p>Fichier interdit</p>";
                }
            }

            // Redirection vers la page d'accueil
            header('Location: ?action=default');
            exit;
        }

        return $post_touite_html;
    }
}
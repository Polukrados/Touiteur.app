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

                try {


                    // ID de l'utilisateur de la session
                    $utilisateurID = $_SESSION['utilisateur']['userID'];

                    // Valeurs du touite
                    $texte = filter_input(INPUT_POST, 'texte', FILTER_SANITIZE_SPECIAL_CHARS);
                    $description = $_POST['texte'];
                    $datePublication = date('Y-m-d H:i:s'); // Date et heure courantes

                    // insertion dans la table touites
                    $query_touite = $connection->prepare("INSERT INTO touites (texte, datePublication) VALUES (:texte, :datePublication)");
                    $query_touite->bindParam(':texte', $texte);
                    $query_touite->bindParam(':datePublication', $datePublication);
                    $query_touite->execute();

                    // Ajoutez cette vérification
                    if ($query_touite->rowCount() === 0) {
                        throw new \Exception("Erreur lors de l'insertion dans la table touites.");
                    }

                    // Si l'insertion dans touites s'est bien déroulée, alors on peut insérer dans touitesutilisateurs
                    $touiteID = $connection->lastInsertId();

                    $query_touite_utilisateur = $connection->prepare("INSERT INTO touitesutilisateurs (touiteID, utilisateurID) VALUES (:touiteID, :utilisateurID)");
                    $query_touite_utilisateur->bindParam(':touiteID', $touiteID);
                    $query_touite_utilisateur->bindParam(':utilisateurID', $utilisateurID);
                    $query_touite_utilisateur->execute();

                    // Analyser le texte pour extraire les tags
                    preg_match_all('/#([a-zA-Z0-9_]+)/', $texte, $matches);

                    $tags = $matches[1];

                    // Insérer les nouveaux tags dans la base de données
                    foreach ($tags as $tag) {
                        $tag = '#' . $tag;
                        $this->insertTagIfNotExists($tag);
                        $tagID = $this->getTagID($tag);
                        $this->associateTagWithTouite($touiteID, $tagID);
                    }

                    $mediaPath = null;
                    $track_name = $_FILES['media']['name'];
                    // Gérer le téléchargement du fichier (si un fichier est ajouté et vérifie son extension)
                    if (isset($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
                        if (substr($track_name, -4) === '.jpg' || substr($track_name, -5) === '.jpeg' || substr($track_name, -4) === '.png') {
                            // fichier uploader
                            $mediaPath = basename($track_name);
                            move_uploaded_file($_FILES['media']['tmp_name'], 'images/' . $mediaPath);

                            // insertion dans la table images
                            $query_image = $connection->prepare("INSERT INTO images (description, cheminFichier) VALUES (:description, :mediaPath)");
                            $query_image->bindParam(':description', $description);
                            $query_image->bindParam(':mediaPath', $mediaPath);

                            $query_image->execute();

                            $imageID = $connection->lastInsertId(); // Récupérer l'ID de la dernière image insérée

                            // insertion dans la table touitesimages
                            $query_touite_image = $connection->prepare("INSERT INTO touitesimages (touiteID, imageID) VALUES (:touiteID, :imageID)");
                            $query_touite_image->bindParam(":touiteID", $touiteID);
                            $query_touite_image->bindParam(":imageID", $imageID);
                            $query_touite_image->execute();
                        } else {
                            $post_touite_html .= "<p>Fichier interdit</p>";
                        }
                    }


                    // Redirection vers la page d'accueil
                    header('Location: ?action=default');
                    exit;
                } catch (\PDOException $e) {
                    // Gérer l'erreur
                    $post_touite_html .= "<p>Erreur lors de la publication du touite.</p>";



                    // Ajouter des informations de débogage
                    file_put_contents('error_log.txt', '[' . date('Y-m-d H:i:s') . '] PDOException: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                } catch (\Exception $e) {
                    // Gérer l'erreur
                    $post_touite_html .= "<p>Erreur lors de la publication du touite.</p>";


                    // Ajouter des informations de débogage
                    file_put_contents('error_log.txt', '[' . date('Y-m-d H:i:s') . '] Exception: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                }
            }

            return $post_touite_html;
        }

    // Fonction pour insérer un tag s'il n'existe pas déjà
    private function insertTagIfNotExists($tag)
    {
        $connection = ConnectionFactory::makeConnection();

        try {
            $checkTagQuery = $connection->prepare("SELECT tagID FROM tags WHERE libelle = :tag");
            $checkTagQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
            $checkTagQuery->execute();

            if (!$checkTagQuery->fetchColumn()) {
                // Le tag n'existe pas, on l'insère
                $insertTagQuery = $connection->prepare("INSERT INTO tags (libelle, description) VALUES (:tag, :description)");
                $insertTagQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
                $insertTagQuery->bindParam(':description', $tag, PDO::PARAM_STR); // Utilisez le même libellé comme description temporaire
                $insertTagQuery->execute();

                // Ajoutez cette vérification
                if ($insertTagQuery->rowCount() === 0) {
                    throw new \Exception("Erreur lors de l'insertion dans la table tags.");
                }
            }
        } catch (\Exception $e) {
            // Gérer l'erreur
            echo 'Erreur lors de l\'insertion du tag : ' . $e->getMessage();
        }
    }


    private function getTagID($tag)
    {
        $connection = ConnectionFactory::makeConnection();

        try {
            $tagIDQuery = $connection->prepare("SELECT tagID FROM tags WHERE libelle = :tag");
            $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
            $tagIDQuery->execute();

            return $tagIDQuery->fetchColumn();
        } catch (\Exception $e) {
            // Affichez des informations de débogage
            echo 'Erreur lors de la récupération du tagID : ' . $e->getMessage();
            return null;
        }
    }

    // Fonction pour associer un tag à un touite
    private function associateTagWithTouite($touiteID, $tagID)
    {
        $connection = ConnectionFactory::makeConnection();

        try {
            // Insérer dans la table touitestags
            $associateTagQuery = $connection->prepare("INSERT INTO touitestags (touiteID, tagID) VALUES (:touiteID, :tagID)");
            $associateTagQuery->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
            $associateTagQuery->bindParam(':tagID', $tagID, PDO::PARAM_INT);
            $associateTagQuery->execute();

            // Ajouter cette vérification
            if ($associateTagQuery->rowCount() === 0) {
                throw new \Exception("Erreur lors de l'insertion dans la table touitestags.");
            }
        } catch (\Exception $e) {
            // Gérer l'erreur
            echo 'Erreur lors de l\'association du tag avec le touite : ' . $e->getMessage();
        }
    }

}
?>

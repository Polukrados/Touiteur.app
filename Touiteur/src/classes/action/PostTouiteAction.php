<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class PostTouiteAction extends Action
{

    public function execute(): string
    {
        $post_touite_html = "";
        $connection = ConnectionFactory::makeConnection();

        if ($this->http_method == "GET") {
            $post_touite_html .= <<<HTML
                  <header> 
                    <p class ='libelle_page_courante'>Publier un nouveau Touite</p>
                    <nav class="menu">
                      <ul>
                        <li><a href="?action=default">Accueil</a></li>
                        <li><a href="?action=logout">Déconnexion</a></li>
                      </ul>
                    </nav>
                  </header>
                  <div class="post-touite">
                    <form class="post_touite_form" action='?action=post-touite' method='post' enctype="multipart/form-data">
                        <h1> Publier votre Touite </h1>
                        <input type='text' placeholder='Titre' name='titre' id='titre' required><br><br>
                        <textarea placeholder='Votre touite ici' name='texte' id='texte' required></textarea><br><br>
                        <input type='file' name='media' id='media'><br><br>
                        <input type='submit' value='Publier'>
                    </form>
                  </div>
                  HTML;
        } else if ($this->http_method == "POST") {
            if (!isset($_SESSION['utilisateur'])) {
                header('Location: ?action=signin');
                exit;
            }
            // Assainir les entrées
            $texte = filter_input(INPUT_POST, 'texte', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $utilisateurID = $_SESSION['utilisateur']['userID'];

            // Gérer le téléchargement du fichier
            $mediaPath = null;
            if (isset($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
                // Valider le fichier ici (type, taille, etc.)
                $mediaPath = 'chemin/vers/le/dossier/uploads/' . basename($_FILES['media']['name']);
                move_uploaded_file($_FILES['media']['tmp_name'], $mediaPath);
                // Vous pouvez également générer un nom de fichier unique pour éviter les écrasements
            }

            $datePublication = date('Y-m-d H:i:s'); // Date et heure courantes

            // Préparer la requête pour insérer les données dans la base de données
            $query = $connection->prepare("INSERT INTO touites (touiteID, texte, datePublication) VALUES (:touiteID, :texte, :datePublication)");
            $query->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
            $query->bindParam(':texte', $texte);
            $query->bindParam(':datePublication', $datePublication);
            $queryImage = $connection->prepare("INSERT INTO images (imageID, description, cheminFichier) VALUES (:imageID :descption, :mediaPath)");
            $queryImage->bindParam(':imageID', $imageID);
            $queryImage->bindParam(':description', $description);
            $queryImage->bindParam(':mediaPath', $mediaPath);
            $query2 = $connection->prepare("INSERT INTO touitesimages (touiteID, imageID) VALUES (:touiteID, :imageID)");
            $query2->bindParam(':touiteID', $touiteID);
            $query2->bindParam(':imageID', $imageID);
            $query3 = $connection->prepare("INSERT INTO touitesutilisateurs (touiteID, utilisateurID) VALUES (:touiteID, :utilisateurID)");
            $query3->bindParam(':touiteID', $touiteID);
            $query3->bindParam(':utilisateurID', $utilisateurID);

            try {
                $query->execute();
                // Redirection ou affichage d'un message de succès
                $post_touite_html .= "<p>Touite publié avec succès !</p>";
            } catch (\PDOException $e) {
                // Gérer l'erreur, peut-être enregistrer dans un fichier de logs
                $post_touite_html .= "<p>Erreur lors de la publication du touite.</p>";
            }
        }

        return $post_touite_html;
    }
}
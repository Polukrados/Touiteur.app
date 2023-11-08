<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class FollowUserAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $db = ConnectionFactory::makeConnection();

        $userID="";

        if (isset($_GET['user_id'])) {
            $userID = intval($_GET['user_id']);

            $query = $db->prepare("SELECT nom, prenom FROM Utilisateurs WHERE utilisateurID = :id");
            $query->bindParam(':id', $userID, PDO::PARAM_INT);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);
            $prenom = $row['prenom'];
            $nom = $row['nom'];
            $pseudo = $prenom . '_' . $nom;

            $query = $db->prepare("SELECT Touites.touiteID, Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle, Images.cheminFichier
                                                    FROM Touites
                                                    LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                                                    LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                                                    LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.touiteID
                                                    LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                                                    LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                                                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                                                    WHERE Utilisateurs.utilisateurID = :user_id
                                                    ORDER BY Touites.datePublication DESC");

            $query->bindParam(':user_id', $userID, PDO::PARAM_INT);
            $query->execute();

            $tweets = '';
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tweetID = $row['touiteID'];
                $userName = $row['prenom'] . '_' . $row['nom'];
                $content = $row['texte'];
                $tagID = $row['tagID'];
                $libelle = $row['libelle'];
                $timestamp = $row['datePublication'];
                $imagePath = $row['cheminFichier'];

                // Tweet court avec un formulaire pour les détails
                $tweetHTML = <<<HTML
                                                        <div class="template-feed">
                                                            <div class="user">Utilisateur: $userName</div>
                                                            <div class="content">$content <a href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a></div>
                                                            <div class="timestamp">Publié le : $timestamp</div>
                                                            <img src="$imagePath" alt="Image associée au tweet">
                                                            <form method="post" action="?action=default">
                                                                <input type="hidden" name="tweet_id" value="$tweetID">
                                                                <input type="submit" value="Voir les détails">
                                                            </form>
                                                        </div>
                                                    HTML;

                $tweets .= $tweetHTML;
            }

            // Page
            $pageContent = <<<HTML
                            <header>
                                <p class="libelle_page_courante">Profil de l'utilisateur : $userName</p>
                                <nav class="menu">
                                    <ul>
                                        <li><a href="?action=add-user">S'inscrire</a></li>
                                        <li><a href="?action=signin">Se connecter</a></li>
                                        <li><a href="?action=default">Retour à l'accueil</a></li>
                                    </ul>
                                </nav>
                            </header>
                            HTML;
        }

        if ($this->http_method === 'GET') {
            // Vérifie si un utilisateur a été sélectionné
            $checkQuery = $db->prepare("SELECT COUNT(*) FROM suivi WHERE suivreID = :followID AND suiviID = :followedID");
            $checkQuery->bindParam(':followID', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':followedID', $userID, PDO::PARAM_INT);
            $checkQuery->execute();
            $existingRelation = $checkQuery->fetchColumn();

            if ($existingRelation === 0) {
                $pageContent .= <<<HTML
                                                <div class="profil">
                                                                    <h3>Nom : $nom</h3>
                                                                    <h3>Prénom : $prenom</h3>
                                                                    <form method="post">
                                                                        <input type="hidden" name="user_id" value="$userID">
                                                                        <input type="submit" value="Suivre cet utilisateur">
                                                                    </form>
                                                                    </div>
                                                                    <div class="tweets">
                                                                    <br>
                                                                    <p>Liste des tweets de l'utilisateur :</p>
                                                                    <br>
                                                                        $tweets
                                                                    </div>
                                            HTML;
            } else {
                $pageContent .= <<<HTML
                                                <div class="profil">
                                                                    <h3>Nom : $nom</h3>
                                                                    <h3>Prénom : $prenom</h3>
                                                                    <p>Vous suivez cet utilisateur.</p>
                                                                    </div>
                                                                    <div class="tweets">
                                                                    <br>
                                                                    <p>Liste des tweets de l'utilisateur :</p>
                                                                    <br>
                                                                        $tweets
                                                                    </div>
                                            HTML;
            }

            return $pageContent;
        } else if ($this->http_method === 'POST') {
            $userID = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
            $pageContent = '';

            if ($userID !== null) {
                // La relation n'existe pas, vous pouvez effectuer l'insertion.
                $insertQuery = $db->prepare("INSERT INTO suivi(suivreID, suiviID) VALUES(:followID, :followedID)");
                $insertQuery->bindParam(':followID', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $insertQuery->bindParam(':followedID', $userID, PDO::PARAM_INT);
                $insertQuery->execute();

                $pageContent = <<<HTML
                                                            <header>
                                                                <p class="libelle_page_courante">Profil de l'utilisateur : $userName</p>
                                                                <nav class="menu">
                                                                    <ul>
                                                                        <li><a href="?action=add-user">S'inscrire</a></li>
                                                                        <li><a href="?action=signin">Se connecter</a></li>
                                                                        <li><a href="?action=default">Retour à l'accueil</a></li>
                                                                    </ul>
                                                                </nav>
                                                            </header>
                                                            <p> Vous suivez maintenant l'utilisateur $userName !</p>
                    HTML;
            }


        }
        return $pageContent;
    }
}

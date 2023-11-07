<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class TagTouiteListAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $db = ConnectionFactory::makeConnection();

        // Vérifie si un utilisateur a été sélectionné
        if (isset($_GET['tag_id'])) {
            $tagID = intval($_GET['tag_id']);

            $query = $db->prepare("SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.prenom, Utilisateurs.nom, Touites.datePublication, Tags.libelle, Images.cheminFichier
                    FROM Touites
                    LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                    LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                    LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.touiteID
                    LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                    LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                    WHERE Tags.TagID = :tag_id
                    ORDER BY Touites.datePublication DESC");

            $query->bindParam(':tag_id', $tagID, PDO::PARAM_INT);
            $query->execute();

            $tweets = '';
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $tweetID = $row['touiteID'];
                            $userID = $row['utilisateurID'];
                            $userName = $row['prenom'] . ' ' . $row['nom'];
                            $content = $row['texte'];
                            $libelle = $row['libelle'];
                            $timestamp = $row['datePublication'];
                            $imagePath = $row['cheminFichier'];

                            // Tweet court avec un formulaire pour les détails
                            $tweetHTML = <<<HTML
                        <div class="tweet">
                            <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$userID'>$userName</a></div>
                            <div class="content">$content $libelle</div>
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
            <p class="libelle_page_courante">$libelle</p>
            <nav class="menu">
                <ul>
                    <li><a href="?action=add-user">S'inscrire</a></li>
                    <li><a href="?action=signin">Se connecter</a></li>
                    <li><a href="?action=default">Retour à l'accueil</a></li>
                </ul>
            </nav>
        </header>
        <div class="tweets">
            $tweets
        </div>
    HTML;

            return $pageContent;
        } else {
            // Si aucun utilisateur n'est sélectionné, redirige vers la page d'accueil
            header('Location: ?action=default');
            exit();
        }
    }
}

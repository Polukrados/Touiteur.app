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

        // Vérifie si un utilisateur a été sélectionné
        if (isset($_GET['user_id'])) {
            $userID = intval($_GET['user_id']);

            $followCheck = $db->prepare("SELECT COUNT(*) FROM Suivi WHERE suivreID = :follow AND suiviID = :followed");
            $followCheck->bindParam(':follow', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $followCheck->bindParam(':followed', $userID, PDO::PARAM_INT);
            $followCheck->execute();

            $count = $followCheck->fetchColumn();

            if ($count == 0) {
                // Si l'utilisateur ne suit pas déjà l'auteur du tweet, on ajoute la relation dans la table
                $follow = $db->prepare("INSERT INTO Suivi(suivreID, suiviID) VALUES(:follow,:followed)");
                $follow->bindParam(':follow', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $follow->bindParam(':followed', $userID, PDO::PARAM_INT);
                $follow->execute();
            }

            $query = $db->query("SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle
                                    FROM Touites
                                    left JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                                    left JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                                    left JOIN TouitesImages ON TouitesImages.TouiteID= Touites.TouiteID
                                    left JOIN Images ON Images.ImageID=TouitesImages.ImageID
                                    LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                                    ORDER BY Touites.datePublication DESC");

                        $tweets = '';
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $tweetID = $row['touiteID'];
                            $currentUserID = $row['utilisateurID'];
                            $userName = $row['prenom'] . ' ' . $row['nom'];
                            $content = $row['texte'];
                            $tagID = $row['tagID'];
                            $libelle = $row['libelle'];
                            $timestamp = $row['datePublication'];

                            $rendu = $db->prepare("SELECT suiviID FROM suivi WHERE suivreID = :id");
                            $rendu->bindParam(':id', $_SESSIOB['utilisateur']['userID'], PDO::PARAM_INT);
                            $rendu->execute();

                            $follow = false;
                            while($res = $rendu->fetch(PDO::FETCH_ASSOC)) {
                                if ($res['suiviID'] == $currentUserID) {
                                    $follow = true;
                                }
                            }

                            if($follow) {
                                // Touit court
                                $tweets .= <<<HTML
                                <div class="tweet">
                                <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$currentUserID'>$userName</a> - Suivi</div>
                                <div class="content">$content <a href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a></div>
                                <div class="timestamp">Publié le : $timestamp</div>
                                <a href="?action=details&tweet_id=$tweetID">Voir les détails</a>
                                </div>
                                HTML;
                            } else {
                                // Touit court
                                $tweets .= <<<HTML
                                <div class="tweet">
                                <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$currentUserID'>$userName</a> - <a href='?action=follow-user&user_id=$currentUserID'>Suivre</a></div>
                                <div class="content">$content <a href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a></div>
                                <div class="timestamp">Publié le : $timestamp</div>
                                <a href="?action=details&tweet_id=$tweetID">Voir les détails</a>
                                </div>
                                HTML;
                            }
                        }

            // Page
            $pageContent = <<<HTML
        <header>
            <p class="libelle_page_courante">Accueil</p>
            <nav class="menu">
                <ul>
                    <li><a href="?action=add-user">S'inscrire</a></li>
                    <li><a href="?action=signin">Se connecter</a></li>
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

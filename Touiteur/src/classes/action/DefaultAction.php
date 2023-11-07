<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class DefaultAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    private function texte($text, $maxLength = 50, $suffix = '...'): string
    {
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . $suffix;
        }
        return $text;
    }

    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $db = ConnectionFactory::makeConnection();
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
                $userID = $row['utilisateurID'];
                $userName = $row['prenom'] . ' ' . $row['nom'];
                $content = $this->texte($row['texte']);                $tagID = $row['tagID'];
                $libelle = $row['libelle'];
                $timestamp = $row['datePublication'];

                // Touit court
                $tweets .= <<<HTML
            <div class="tweet">
                <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$userID'>$userName</a></div>
                <div class="content">$content <a href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a></div>
                <div class="timestamp">Publié le : $timestamp</div>
                <a href="?action=details&tweet_id=$tweetID">Voir les détails</a>
            </div>
        HTML;
            }

            // Page
            $pageContent = <<<HTML
        <header>
            <p class="libelle_page_courante">Accueil</p>
            <nav class="menu">
                <ul>
                    <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                    <li><a href="?action=add-user">S'inscrire</a></li>
                    <li><a href="?action=signin">Se connecter</a></li>
                </ul>
            </nav>
        </header>
        <div class="tweets">
            $tweets
        </div>
    HTML;
        }
        return $pageContent;
    }
}

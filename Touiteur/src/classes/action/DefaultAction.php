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

    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $db = ConnectionFactory::makeConnection();
            $query = $db->query("SELECT Touites.touiteID, Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication
                        FROM Touites
                        LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                        LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                        LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.touiteID
                        LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                        ORDER BY Touites.datePublication DESC");

            $tweets = '';
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tweetID = $row['touiteID'];
                $userName = $row['prenom'] . ' ' . $row['nom'];
                $content = $row['texte'];
                $timestamp = $row['datePublication'];

                // Touit court
                $tweets .= <<<HTML
            <div class="tweet">
                <div class="user">Utilisateur: $userName</div>
                <div class="content">$content</div>
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

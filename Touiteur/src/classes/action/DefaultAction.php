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
        $db = ConnectionFactory::makeConnection();
        $query = $db->query("SELECT Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Images.cheminFichier
                            FROM Touites
                            INNER JOIN Utilisateurs ON Touites.userID = Utilisateurs.userID
                            LEFT JOIN TouitesImages ON Touites.touiteID = TouitesImages.touiteID
                            LEFT JOIN Images ON TouitesImages.imageID = Images.imageID
                            ORDER BY Touites.datePublication DESC");

        $tweets = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $userName = $row['prenom'] . ' ' . $row['nom'];
            $content = $row['texte'];
            $timestamp = $row['datePublication'];
            $imagePath = $row['cheminFichier'];

            // Tweet
            $tweetHTML = <<<HTML
                <div class="tweet">
                    <div class="user">Utilisateur: $userName</div>
                    <div class="content">$content</div>
                    <div class="timestamp">Publié le : $timestamp</div>
                    <img src="$imagePath" alt="Image associée au tweet">
                </div>
            HTML;

            $tweets .= $tweetHTML;
        }

        // Page
        $pageContent = <<<HTML
            <header> 
                <p class="libelle_page_courante">Accueil</p> 
                <nav class="menu">
                    <ul>
                        <li><a href="?action=accueil">S'inscrire</a></li>
                        <li><a href="?action=accueil">Se connecter</a></li>
                    </ul>
                </nav>
            </header>
            <div class="tweets">
                $tweets
            </div>
        HTML;

        return $pageContent;
    }
}
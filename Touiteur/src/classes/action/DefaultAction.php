<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class DefaultAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $db = ConnectionFactory::makeConnection();
        $query = $db->query("SELECT * FROM tweets ORDER BY timestamp DESC");
        $tweets = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $user_id = $row['user_id']; // Supposons que vous avez une colonne "user_id" pour l'identifiant de l'utilisateur
            $content = $row['content'];
            $timestamp = $row['timestamp'];

            // Tweet
            $tweetHTML = <<<HTML
                <div class="tweet">
                    <div class="user">User ID: $user_id</div>
                    <div class="content">$content</div>
                    <div class="timestamp">Posted at: $timestamp</div>
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

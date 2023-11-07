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

        if ($this->http_method === 'GET') {
            $query = $db->query("SELECT Touites.touiteID, Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Images.cheminFichier
                        FROM Touites
                        left JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                        left JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                        left JOIN TouitesImages ON TouitesImages.TouiteID= Touites.TouiteID
                        left JOIN Images ON Images.ImageID=TouitesImages.ImageID
                        ORDER BY Touites.datePublication DESC");

            $tweets = '';
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tweetID = $row['touiteID'];
                $userName = $row['prenom'] . ' ' . $row['nom'];
                $content = $row['texte'];
                $timestamp = $row['datePublication'];
                $imagePath = $row['cheminFichier'];

                // Tweet court avec un formulaire pour les détails
                $tweetHTML = <<<HTML
            <div class="tweet">
                <div class="user">Utilisateur: $userName</div>
                <div class="content">$content</div>
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
            if (isset($_POST['tweet_id'])) {
                $tweetID = intval($_POST['tweet_id']);
                $query = $db->prepare("SELECT Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Evaluations.note
                        FROM Touites
                        INNER JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                        INNER JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                        LEFT JOIN Evaluations ON Touites.touiteID = Evaluations.touiteID
                        WHERE Touites.touiteID = :tweet_id");
                $query->bindParam(':tweet_id', $tweetID, PDO::PARAM_INT);
                $query->execute();

                $row = $query->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $userName = $row['prenom'] . ' ' . $row['nom'];
                    $content = $row['texte'];
                    $timestamp = $row['datePublication'];
                    $imagePath = $row['cheminFichier'];
                    $score = $row['note'] ?? 'N/A'; // Si la note n'est pas disponible

                    // Tweet en détail
                    $tweetHTML = <<<HTML
                <div class="tweet-detail">
                    <div class="user">Utilisateur: $userName</div>
                    <div class="content">$content</div>
                    <div class="timestamp">Publié le : $timestamp</div>
                    <img src="$imagePath" alt="Image associée au tweet">
                    <div class="score">Score : $score</div>
                </div>
            HTML;

                    // Lien pour retourner à la liste des tweets
                    $backLink = '<a href="?action=default">Retour à la liste des tweets</a>';

                    // Page
                    $pageContent = <<<HTML
                <header> 
                    <p class="libelle_page_courante">Détails du Tweet</p>
                </header>
                <div class="tweet-details">
                    $tweetHTML
                    $backLink
                </div>
            HTML;

                    return $pageContent;
                } else {
                    return 'Tweet non trouvé.';
                }
            } else {
                return 'ID';
            }
        }
    }
}

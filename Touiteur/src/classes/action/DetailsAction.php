<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class DetailsAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        if ($this->http_method === 'GET' && isset($_GET['tweet_id'])) {
            $db = ConnectionFactory::makeConnection();
            $tweetID = intval($_GET['tweet_id']);
            $query = $db->prepare("SELECT Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Evaluations.note, Images.cheminFichier
                FROM Touites
                INNER JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                INNER JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                LEFT JOIN Evaluations ON Touites.touiteID = Evaluations.touiteID
                LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.TouiteID
                LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                WHERE Touites.touiteID = :tweet_id");
            $query->bindParam(':tweet_id', $tweetID, PDO::PARAM_INT);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $userName = $row['prenom'] . ' ' . $row['nom'];
                $content = $row['texte'];
                $timestamp = $row['datePublication'];
                $imagePath = $row['cheminFichier'] ? "images/" . $row['cheminFichier'] : '';
                $score = $row['note'] ?? 0;

                $imageHTML = $imagePath ? "<img src=\"$imagePath\" alt=\"Image associée au tweet\">" : '';

                // Touit long
                $tweetHTML = <<<HTML
                            <div class="tweet-detail">
                            <div class="user">Utilisateur: $userName</div>
                            <div class="content">$content</div>
                            <div class="timestamp">Publié le : $timestamp</div>
                            $imageHTML
                            <div class="score">Score : $score</div>
                            </div>
                            HTML;

                // Page
                $pageContent = <<<HTML
                <header>
                    <a href="?action=default"><img src="images/retour_arriere.png" alt="Image associée au tweet"></a> 
                    <p class="libelle_page_courante">Détails du Tweet</p>
                </header>
                <div class="tweet-details">
                    $tweetHTML
                </div>
            HTML;

                return $pageContent;
            }
        }

        return 'Touit non trouvé.';
    }
}

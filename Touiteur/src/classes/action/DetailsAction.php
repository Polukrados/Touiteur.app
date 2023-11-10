<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

/**
 * Action permettant d'afficher les détails d'un touite.
 */
class DetailsAction extends Action
{
    // Constructeur
    public function __construct()
    {
        parent::__construct();
    }

    // Méthode d'exécution
    public function execute(): string
    {
        // Vérifiez les identifiants de connexion et affichez des messages de débogage en cas d'erreur
        if ($this->http_method === 'GET' && isset($_GET['tweet_id'])) {
            $db = ConnectionFactory::makeConnection();
            $tweetID = intval($_GET['tweet_id']);
            // Requête
            // La requete permet de récupérer les informations du touite, de l'utilisateur qui l'a publié, de l'image associée et de la note associée
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

            // Si le touite existe
            if ($row) {
                // Récupération des informations
                $userName = $row['prenom'] . ' ' . $row['nom'];
                $content = $row['texte'];
                $timestamp = $row['datePublication'];
                $imagePath = $row['cheminFichier'] ? "images/" . $row['cheminFichier'] : '';
                $score = $row['note'] ?? 0;

                // Image
                $imageHTML = $imagePath ? "<img src=\"$imagePath\" alt=\"Image associée au tweet\">" : '';

                // Touit long
                $tweetHTML = <<<HTML
                            <div class="tweet-detail">
                            <div class="user"><b>Utilisateur:</b> $userName</div>
                            <div class="content"><b>Touite :</b><br>$content</div>
                            <div class="timestamp"><b>Publié le :</b> $timestamp</div>
                            $imageHTML
                            <div class="score">Score : $score</div>
                            </div>
                            HTML;
                // Page
                $pageContent = <<<HTML
                <header>
                    <a class="retour-arriere" href="?action=default"><img src="images/retour_arriere.png" alt="Flèche retour arrière"></a>
                    <p class="libelle_page_courante">       
                        Détails du Tweet
                    </p>
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

<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use PDO;
use PDOException;

class ProfileUserAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $db = ConnectionFactory::makeConnection();

        if (isset($_GET['user_id'])) {
            $userID = intval($_GET['user_id']);

            $query = $db->prepare("SELECT nom, prenom FROM Utilisateurs WHERE utilisateurID = :id");
            $query->bindParam(':id', $userID, PDO::PARAM_INT);
            $query->execute();

            $row = $query->fetch(PDO::FETCH_ASSOC);
            $prenom = $row['prenom'];
            $nom = $row['nom'];

            $scoremoyen = 'Score moyen de l\'utilisateur : ';
            $query = $db->prepare("SELECT AVG(note) FROM Evaluations
                                                    INNER JOIN TouitesUtilisateurs ON Evaluations.touiteID = TouitesUtilisateurs.TouiteID
                                                    WHERE TouitesUtilisateurs.utilisateurID = :id");
            $query->bindParam(':id', $userID, PDO::PARAM_INT);
            $query->execute();
            $score = $query->fetchColumn();

            $count = $db->prepare("SELECT * FROM TouitesUtilisateurs WHERE utilisateurID = :id");
            $count->bindParam(':id', $userID, PDO::PARAM_INT);
            $count->execute();

            if ($score == null) {
                if ($count->fetchColumn() == null) {
                    $scoremoyen .= 'Cet utilisateur n\'a pas encore envoyé de touite.';
                } else {
                    $scoremoyen .= 'Les touites de cet utilisateur n\'ont pas encore été notés.';
                }
            } else {
                $scoremoyen .= $score;
            }

            $listefollowers = 'Liste des utilisateurs qui suivent cette personne :<br><br>';
            $query = $db->prepare("SELECT Utilisateurs.prenom, Utilisateurs.nom
                                                    FROM Utilisateurs
                                                    INNER JOIN Suivi ON Utilisateurs.utilisateurID = Suivi.suivreID
                                                    WHERE suiviID = :id");

            $query->bindParam(':id', $userID, PDO::PARAM_INT);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $prenomfollower = $row['prenom'];
                $nomfollower = $row['nom'];
                $listefollowers .= $prenomfollower . '_' . $nomfollower . '<br>';
            }

            $tweets = parent::generationAction("SELECT Touites.touiteID, Touites.texte, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle, Utilisateurs.utilisateurID
                                                    FROM Touites
                                                    LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                                                    LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                                                    LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.touiteID
                                                    LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                                                    LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                                                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                                                    WHERE Utilisateurs.utilisateurID = :user_id
                                                    ORDER BY Touites.datePublication DESC
                                                    LIMIT :limit OFFSET :offset", false, false, true);

        }

        if ($this->http_method === 'GET') {
            $checkQuery = $db->prepare("SELECT COUNT(*) FROM suivi WHERE suivreID = :followID AND suiviID = :followedID");
            $checkQuery->bindParam(':followID', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':followedID', $userID, PDO::PARAM_INT);
            $checkQuery->execute();
            $existingRelation = $checkQuery->fetchColumn();


            if ($userID == $_SESSION['utilisateur']['userID']) {
                $pageContent = "<p>C'est vous.</p>";
            } else {
                if ($existingRelation === 0) {

                    $follow = 0;
                    if (!isset($_SESSION['utilisateur'])) {
                        // Redirection vers la page de connexion
                        header("Location: ?action=signin");
                        exit();
                    } else {
                        $pageContent = "<form method='post' class='follow-form'>
                        <input type='hidden' name='user_id' value='$userID'>
                        <input type='hidden' name='follow' value='$follow'>
                        <input type='submit' class='follow-btn' value='Suivre cet utilisateur'>
                        </form>";
                    }

                } else {
                    $follow = 1;
                    $pageContent = "<p class='suivi'>Vous suivez déjà cet utilisateur</p>
                                    <form method='post' class='follow-form'>
                                     <input type='hidden' name='user_id' value='$userID'>
                                     <input type='hidden' name='follow' value='$follow'>
                                     <input type='submit' class='follow-btn' value='Ne plus suivre cet utilisateur'>
                                     </form>";
                }
            }

            return <<<HTML
                    <div class="profile-container">
                   <div class="profile-header">
                          <h1>$nom $prenom</h1>
                          $pageContent
                          <br>
                          $scoremoyen
                          <br>
                          <p class="followers-count">$listefollowers</p>
                   </div>
                   <a class='logo_touiteur' href='?action=default'><img src='images/logo_touiteur.png' alt='Logo de Touiteur'></a>
                   <div class="tweets-container">
                          <div class="tweets-list">
                              $tweets
                          </div>
                   </div>
               </div>
               HTML;
        } else {
            $userID = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
            $follow = isset($_POST['follow']) ? intval($_POST['follow']) : null;
            $pageContent = '';

            if ($follow === 0) {
                if ($userID !== null) {
                    // La relation n'existe pas alors on fait l'insertion dans la table
                    $insertQuery = $db->prepare("INSERT INTO suivi(suivreID, suiviID) VALUES(:followID, :followedID)");
                    $insertQuery->bindParam(':followID', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                    $insertQuery->bindParam(':followedID', $userID, PDO::PARAM_INT);
                    try {
                        $insertQuery->execute();
                        $pageContent = "<p class='suivi'>Vous suivez déjà cet utilisateur</p>
                            <form method='post' class='follow-form'>
                                <input type='hidden' name='user_id' value='$userID'>
                                <input type='hidden' name='follow' value='1'>
                                <input type='submit' class='follow-btn' value='Ne plus suivre cet utilisateur'>
                            </form>";
                    } catch (PDOException $e) {
                        header("Location: ?action=signin");
                        exit();
                    }


                }
            } else if ($follow === 1) {
                if ($userID !== null) {
                    // On supprime, on veut plus le suivre
                    $deleteQuery = $db->prepare("DELETE FROM suivi WHERE suivreID = :followID AND suiviID = :followedID");
                    $deleteQuery->bindParam(':followID', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                    $deleteQuery->bindParam(':followedID', $userID, PDO::PARAM_INT);
                    $deleteQuery->execute();

                    $pageContent = "<form method='post' class='follow-form'>
                                <input type='hidden' name='user_id' value='$userID'>
                                <input type='hidden' name='follow' value='0'>
                                <input type='submit' class='follow-btn' value='Suivre cet utilisateur'>
                            </form>";
                }
            }
            return <<<HTML
        <div class="profile-container">
            <div class="profile-header">
                <h1>$nom $prenom</h1>
                $pageContent
                <br>
                $scoremoyen
                <br>
                <p class="followers-count">$listefollowers</p>
            </div>
            <a class='logo_touiteur' href='?action=default'><img src='images/logo_touiteur.png' alt='Logo de Touiteur'></a>
            <div class="tweets-container">
                <div class="tweets-list">
                    $tweets
                </div>
            </div>
        </div>
    HTML;
        }
    }
}

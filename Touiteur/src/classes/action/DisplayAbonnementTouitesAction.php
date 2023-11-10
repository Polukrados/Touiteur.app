<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\user\User;
use PDO;

class DisplayAbonnementTouitesAction extends Action
{

    public function execute(): string
    {
        $pageContent="";
        if ($this->http_method == "GET"){
            $pageContent = parent::generationAction("SELECT t.touiteID, t.texte, u.utilisateurID, u.nom, u.prenom, t.datePublication, Tags.tagID, Tags.libelle
FROM Touites t
LEFT JOIN TouitesUtilisateurs ON t.touiteID = TouitesUtilisateurs.TouiteID
LEFT JOIN Utilisateurs u ON TouitesUtilisateurs.utilisateurID = u.utilisateurID
LEFT JOIN TouitesTags ON t.touiteID = TouitesTags.TouiteID
LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
LEFT JOIN abonnementtags ON Tags.tagID = abonnementtags.tagID
LEFT JOIN suivi ON u.utilisateurID = suivi.suiviID
WHERE (abonnementtags.utilisateurID = :user_id1 OR suivi.suivreID = :user_id2)
ORDER BY t.datePublication DESC
LIMIT :limit OFFSET :offset;
",false,false,false,true);

        }else{
            if (isset($_POST['f'])) {
                $tagToFollow = htmlspecialchars($_POST['f']);
                $this->followTag($tagToFollow);
            }
            if (isset($_POST['u'])) {
                $tagToUnfollow = htmlspecialchars($_POST['u']);
                $this->unfollowTag($tagToUnfollow);
            }
            header("Location: ?action=display-abo&user_id={$_SESSION['utilisateur']['userID']}");
            exit();
        }
        return $pageContent;
    }


    private function followTag($tag)
    {
        // Effectuez l'insertion dans la table Abonnementtags
        $db = ConnectionFactory::makeConnection();

        $tagIDQuery = $db->prepare("SELECT tagID FROM Tags WHERE libelle = :tag");
        $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
        $tagIDQuery->execute();

        $tagID = $tagIDQuery->fetchColumn();

        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM Abonnementtags
                                    WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();
            $check = $checkQuery->fetchColumn();
            if($check==0) {
                $followQuery = $db->prepare("INSERT INTO Abonnementtags(utilisateurID, tagID) VALUES(:user, :tag)");
                $followQuery->bindValue(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $followQuery->bindValue(':tag', $tagID, PDO::PARAM_INT);
                $followQuery->execute();
            }
        }
    }

    // Récupère les tag follow par l'utilisateur
    private function unfollowTag($tag)
    {
        // Effectuez l'insertion dans la table Abonnementtags
        $db = ConnectionFactory::makeConnection();

        $tagIDQuery = $db->prepare("SELECT tagID FROM Tags WHERE libelle = :tag");
        $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
        $tagIDQuery->execute();

        $tagID = $tagIDQuery->fetchColumn();

        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM Abonnementtags
                                                WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();

            $check = $checkQuery->fetchColumn();
            if($check!==0) {
                $deleteQuery = $db->prepare("DELETE FROM Abonnementtags WHERE utilisateurID = :user AND tagID = :tag");
                $deleteQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $deleteQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
                $deleteQuery->execute();
            }
        }
    }
}
<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

/**
 * Action permettant d'afficher les touites des utilisateurs suivis par l'utilisateur connecté
 */
class DisplayAbonnementTouitesAction extends Action
{

    // Méthode qui exécute l'action
    public function execute(): string
    {
        $pageContent="";
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if ($this->http_method == "GET") {
            $pageContent = parent::generationAction("SELECT DISTINCT t.touiteID, t.texte, u.utilisateurID, u.nom, u.prenom, t.datePublication, Tags.tagID, Tags.libelle
FROM Touites t
LEFT JOIN TouitesUtilisateurs ON t.touiteID = TouitesUtilisateurs.TouiteID
LEFT JOIN Utilisateurs u ON TouitesUtilisateurs.utilisateurID = u.utilisateurID
LEFT JOIN TouitesTags ON t.touiteID = TouitesTags.TouiteID
LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
LEFT JOIN AbonnementTags ON Tags.tagID = AbonnementTags.tagID
LEFT JOIN Suivi ON u.utilisateurID = Suivi.suiviID
WHERE (AbonnementTags.utilisateurID = :user_id1 OR Suivi.suivreID = :user_id2)
ORDER BY t.datePublication DESC
LIMIT :limit OFFSET :offset;", false, false, false, true);

            // On récupère les tags de l'utilisateur
        }else{
            if (isset($_POST['f'])) {
                $tagToFollow = htmlspecialchars($_POST['f']);
                $this->followTag($tagToFollow);
            }
            // On récupère les tags de l'utilisateur
            if (isset($_POST['u'])) {
                $tagToUnfollow = htmlspecialchars($_POST['u']);
                $this->unfollowTag($tagToUnfollow);
            }
            header("Location: ?action=display-abo&user_id={$_SESSION['utilisateur']['userID']}");
            exit();
        }
        return $pageContent;
    }


    // Permet de suivre un tag
    private function followTag($tag)
    {
        // Effectuez l'insertion dans la table Abonnementtags
        $db = ConnectionFactory::makeConnection();

        $tagIDQuery = $db->prepare("SELECT tagID FROM Tags WHERE libelle = :tag");
        $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
        $tagIDQuery->execute();

        $tagID = $tagIDQuery->fetchColumn();

        // Si le tag existe alors on l'ajoute à la table Abonnementtags
        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM AbonnementTags
                                    WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();
            $check = $checkQuery->fetchColumn();
            if ($check == 0) {
                $followQuery = $db->prepare("INSERT INTO AbonnementTags(utilisateurID, tagID) VALUES(:user, :tag)");
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

        // Si le tag existe alors on l'ajoute à la table Abonnementtags
        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM AbonnementTags
                                                WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();

            $check = $checkQuery->fetchColumn();
            if ($check !== 0) {
                $deleteQuery = $db->prepare("DELETE FROM AbonnementTags WHERE utilisateurID = :user AND tagID = :tag");
                $deleteQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $deleteQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
                $deleteQuery->execute();
            }
        }
    }
}
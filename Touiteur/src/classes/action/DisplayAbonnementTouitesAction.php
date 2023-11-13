<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

/**
 * Action permettant d'afficher les touites des utilisateurs suivis par l'utilisateur connecté
 */
class DisplayAbonnementtouitesAction extends Action
{

    // Méthode qui exécute l'action
    public function execute(): string
    {
        $pageContent="";
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if ($this->http_method == "GET") {
            $pageContent = parent::generationAction("SELECT DISTINCT t.touiteID, t.texte, u.utilisateurID, u.nom, u.prenom, t.datePublication, tags.tagID, tags.libelle
FROM touites t
LEFT JOIN touitesutilisateurs ON t.touiteID = touitesutilisateurs.TouiteID
LEFT JOIN utilisateurs u ON touitesutilisateurs.utilisateurID = u.utilisateurID
LEFT JOIN touitestags ON t.touiteID = touitestags.TouiteID
LEFT JOIN tags ON touitestags.TagID = tags.TagID
LEFT JOIN abonnementtags ON tags.tagID = abonnementtags.tagID
LEFT JOIN suivi ON u.utilisateurID = suivi.suiviID
WHERE (abonnementtags.utilisateurID = :user_id1 OR suivi.suivreID = :user_id2)
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
        // Effectuez l'insertion dans la table abonnementtags
        $db = ConnectionFactory::makeConnection();

        $tagIDQuery = $db->prepare("SELECT tagID FROM tags WHERE libelle = :tag");
        $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
        $tagIDQuery->execute();

        $tagID = $tagIDQuery->fetchColumn();

        // Si le tag existe alors on l'ajoute à la table abonnementtags
        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM abonnementtags
                                    WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();
            $check = $checkQuery->fetchColumn();
            if ($check == 0) {
                $followQuery = $db->prepare("INSERT INTO abonnementtags(utilisateurID, tagID) VALUES(:user, :tag)");
                $followQuery->bindValue(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $followQuery->bindValue(':tag', $tagID, PDO::PARAM_INT);
                $followQuery->execute();
            }
        }
    }

    // Récupère les tag follow par l'utilisateur
    private function unfollowTag($tag)
    {
        // Effectuez l'insertion dans la table abonnementtags
        $db = ConnectionFactory::makeConnection();

        $tagIDQuery = $db->prepare("SELECT tagID FROM tags WHERE libelle = :tag");
        $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
        $tagIDQuery->execute();

        $tagID = $tagIDQuery->fetchColumn();

        // Si le tag existe alors on l'ajoute à la table abonnementtags
        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM abonnementtags
                                                WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();

            $check = $checkQuery->fetchColumn();
            if ($check !== 0) {
                $deleteQuery = $db->prepare("DELETE FROM abonnementtags WHERE utilisateurID = :user AND tagID = :tag");
                $deleteQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $deleteQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
                $deleteQuery->execute();
            }
        }
    }
}
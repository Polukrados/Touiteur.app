<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;

class LikeAction extends Action
{

    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            if (isset($_SESSION['utilisateur']['userID'])) {
                $touiteID = $_GET['tweet_id'];

                $db = ConnectionFactory::makeConnection();
                $query1 = "SELECT * FROM touites WHERE touiteID = :touiteID";
                $st1 = $db->prepare($query1);
                $st1->bindParam(":touiteID", $touiteID);
                $st1->execute();
                $data = $st1->fetch();

                if ($data) {
                    $query2 = "SELECT * FROM evaluations WHERE touiteID = :touiteID AND utilisateurID = :utilisateurID";
                    $st2 = $db->prepare($query2);
                    $st2->bindParam(":touiteID", $touiteID);
                    $st2->bindParam(":utilisateurID", $_SESSION['utilisateur']['userID']);
                    $st2->execute();
                    $data2 = $st2->fetch();
                    if ($data2) {
                        if ($data2["evalue"] == 0) {
                            $query3 = "UPDATE touites SET note = note + 1 WHERE touiteID = :touiteID";
                            $st3 = $db->prepare($query3);
                            $st3->bindParam(":touiteID", $touiteID);
                            $st3->execute();

                            $query4 = "UPDATE evaluations SET evalue = 1 WHERE touiteID = :touiteID AND utilisateurID = :utilisateurID";
                            $st4 = $db->prepare($query4);
                            $st4->bindParam(":touiteID", $touiteID);
                            $st4->bindParam(":utilisateurID", $_SESSION['utilisateur']['userID']);
                            $st4->execute();
                        }
                    } else {
                        $query5 = "INSERT INTO evaluations (touiteID, utilisateurID, evalue) VALUES (:touiteID, :utilisateurID, 1)";
                        $st5 = $db->prepare($query5);
                        $st5->bindParam(":touiteID", $touiteID);
                        $st5->bindParam(":utilisateurID", $_SESSION['utilisateur']['userID']);
                        $st5->execute();

                        $query6 = "UPDATE touites SET note = note + 1 WHERE touiteID = :touiteID";
                        $st6 = $db->prepare($query6);
                        $st6->bindParam(":touiteID", $touiteID);
                        $st6->execute();
                    }
                } else {
                    $query7 = "INSERT INTO touites (note) VALUES (1) WHERE touiteID = :touiteID";
                    $st7 = $db->prepare($query7);
                    $st7->bindParam(":touiteID", $touiteID);
                    $st7->execute();
                }

                $pageContent = parent::generationAction(
                    "SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle
                            FROM Touites
                            LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                            LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                            LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.TouiteID
                            LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                            LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                            LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                            ORDER BY Touites.datePublication DESC
                            LIMIT :limit OFFSET :offset");

            } else {
                header('Location: ?action=signin');
                exit();
            }
        }
        return $pageContent;
    }
}
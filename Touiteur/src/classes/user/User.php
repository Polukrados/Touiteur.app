<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class User{
    private int $userID;
    private string $pseudo;
    private string $email;
    private string $mdp;

    // Constructeur
    public function __construct(int $userID, string $nom, string $prenom, string $email, string $mdp){
        $this->userID = $userID;
        $this->pseudo = $prenom . "_" . $nom;
        $this->email = $email;
        $this->mdp = $mdp;
    }

    // Récupère les follows de l'utilisateur
    public function getFollow() :array{
        $db = ConnectionFactory::makeConnection();
        $query = 'SELECT suiviID FROM suivi WHERE suivreID = :suiveur';
        $st = $db->prepare($query);
        $st->bindParam(":suiveur", $_SESSION['utilisateur']['userID']);
        $st->execute();
        $tabFollow = [];
        while ($data = $st->fetch()){
            $tabFollow[] = $data['suiviID'];
        }

        return $tabFollow;
    }

    // Récupère l'id des touites des follows de l'utilisateur
    public function getTouitIDFromFollow(): array
    {
        $tabFollow = $this->getFollow();
        $tabTouitFollow = [];

        $db = ConnectionFactory::makeConnection();
        foreach ($tabFollow as $follow) {
            $query = "SELECT TouiteID  
                      FROM touitesutilisateurs
                      WHERE utilisateurID = :followID";

            $st = $db->prepare($query);
            $st->bindParam(":followID", $follow);
            $st->execute();
            $data = $st->fetch();
            $tabTouitFollow[] = $data['TouiteID'];
        }
        return $tabTouitFollow;
    }

    public function getTouitFollow(): string{
        $tab_touites_follow = $this->getTouitIDFromFollow();
        $tweets = "";

        $db = ConnectionFactory::makeConnection();
        foreach ($tab_touites_follow as $touit) {
            $query = "SELECT t.touiteID, t.texte, u.utilisateurID, u.nom, u.prenom, t.datePublication, Tags.tagID, Tags.libelle
                    FROM Touites t
                    left JOIN TouitesUtilisateurs ON t.touiteID = TouitesUtilisateurs.TouiteID
                    left JOIN Utilisateurs u ON TouitesUtilisateurs.utilisateurID = u.utilisateurID
                    left JOIN TouitesImages ON TouitesImages.TouiteID= t.TouiteID
                    left JOIN Images ON Images.ImageID=TouitesImages.ImageID
                    LEFT JOIN TouitesTags ON t.touiteID = TouitesTags.TouiteID
                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                    WHERE t.touiteID = :touit
                    ORDER BY t.datePublication DESC";
            $st = $db->prepare($query);
            $st->bindParam(':touit', $touit);

            $data = $st->fetch();
            if ($st->execute()) {
                while ($data = $st->fetch()) {
                    $touiteID = $data['touiteID'];
                    $userID = $data['utilisateurID'];
                    $userName = $data['prenom'] . ' ' . $data['nom'];
                    $tagID = $data['tagID'];
                    $libelle = $data['libelle'];
                    $timestamp = $data['datePublication'];

                    // Touit court
                    $tweets .= <<<HTML
                    <div class="tweet">
                        <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$userID'>$userName</a></div>
                        <div class="timestamp">Publié le : $timestamp</div>
                        <a href="?action=details&tweet_id=$touiteID">Voir les détails</a>
                    </div>
                    HTML;
                }
            }
        }
        return $tweets;
    }

    // GETTERS
    public function getPseudo(): string {
        return $this->pseudo;
    }
}
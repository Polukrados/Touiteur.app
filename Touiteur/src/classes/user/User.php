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
        $st->bindParam(":suiveur", $_SESSION['userID']['utilisateurID']);
        $st->execute();

        $tabFollow = [];
        while ($data = $st->fetch()){
            $this->tabFollow[] = $data;
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
            $query = "SELECT imageID 
                      FROM images INNER JOIN  ";

            $st = $db->prepare($query);
            $st->bindParam(":followID", $follow);
            $tabTouitFollow[] = $st->fetch();
        }
        return $tabTouitFollow;
    }

    public function getTouitFollow():string{
        $tab_touites_follow = $this->getTouitIDFromFollow();
        $pageContent = "";
        $db = ConnectionFactory::makeConnection();
        $query = "SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle
                    FROM Touites
                    left JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                    left JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                    left JOIN TouitesImages ON TouitesImages.TouiteID= Touites.TouiteID
                    left JOIN Images ON Images.ImageID=TouitesImages.ImageID
                    LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                    ORDER BY Touites.datePublication DESC";
        $st = $db->prepare($query);
        $tweets = '';
        while ($data = $st->fetch()) {
            $tweetID = $data['touiteID'];
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
                            <a href="?action=details&tweet_id=$tweetID">Voir les détails</a>
                        </div>
                        HTML;
        }

        return $tweets;
    }

    // GETTERS
    public function getPseudo(): string {
        return $this->pseudo;
    }
}
<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class User{
    // Attributs utilisateur
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

    /**********************************************************
     *                                                        *
     * FONCTIONS POUR RECUPERER LES TOUITES DE SES ABONNEMENTS *
     *                                                        *
     **********************************************************/
    // Récupère les id des utilisateurs abonné par l'utilisateur
    public function getFollow(): array{
        $db = ConnectionFactory::makeConnection();
        $query = 'SELECT suiviID 
                  FROM suivi 
                  WHERE suivreID = :suiveur';
        $st = $db->prepare($query);
        $st->bindParam(":suiveur", $_SESSION['utilisateur']['userID']);
        $st->execute();
        $tabTagFollow = [];
        while ($data = $st->fetch()){
            $tabTagFollow[] = $data['suiviID'];
        }

        return $tabTagFollow;
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
            while($data = $st->fetch()) {
                $tabTouitFollow[] = $data['TouiteID'];
            }
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
                      LEFT JOIN TouitesUtilisateurs ON t.touiteID = TouitesUtilisateurs.TouiteID
                      LEFT JOIN Utilisateurs u ON TouitesUtilisateurs.utilisateurID = u.utilisateurID
                      LEFT JOIN TouitesImages ON TouitesImages.TouiteID= t.TouiteID
                      LEFT JOIN Images ON Images.ImageID=TouitesImages.ImageID
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


    /***************************************************************************
     *                                                                         *
     * FONCTIONS POUR RECUPERER LES TOUITES CONTENANT LES TAGS OU IL EST ABONNE *
     *                                                                         *
     ***************************************************************************/
    // Récupère les tag follow par l'utilisateur
    public function getTagFollow() :array{
        $db = ConnectionFactory::makeConnection();
        $query = 'SELECT tagID 
                  FROM abonnementtags 
                  WHERE utilisateurID = :utilisateurID';
        $st = $db->prepare($query);
        $st->bindParam(":utilisateurID", $_SESSION['utilisateur']['userID']);
        $st->execute();
        $tabFollow = [];
        while ($data = $st->fetch()){
            $tabFollow[] = $data['tagID'];
        }

        return $tabFollow;
    }

    // Récupère l'id des touites des tag follows par l'utilisateur
    public function getTouitIDFromTagFollow(): array
    {
        $tabTagFollow = $this->getTagFollow();
        $tabTouitIdTagFollow = [];

        $db = ConnectionFactory::makeConnection();
        foreach ($tabTouitIdTagFollow as $tag_follow) {
            $query = "SELECT TouiteID  
                      FROM touitestags
                      WHERE utilisateurID = :followTagID";

            $st = $db->prepare($query);
            $st->bindParam(":followTagID", $tag_follow);
            $st->execute();
            $data = $st->fetch();
            $tabTouitIdTagFollow[] = $data['TouiteID'];
        }
        return $tabTouitIdTagFollow;
    }


    // GETTERS
    public function getPseudo(): string {
        return $this->pseudo;
    }
}
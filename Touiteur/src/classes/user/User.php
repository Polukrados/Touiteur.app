<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class User
{
    private int $userID;
    private string $pseudo;
    private string $email;
    private string $mdp;

    public function __construct(int $userID, string $nom, string $prenom, string $email, string $mdp)
    {
        $this->userID = $userID;
        $this->pseudo = $prenom . "_" . $nom;
        $this->email = $email;
        $this->mdp = $mdp;
    }

    /***********************************************************
     *                                                         *
     * FONCTIONS POUR RECUPERER LES TOUITES DE SES ABONNEMENTS *
     *                                                         *
     **********************************************************/
    // Récupère les id des utilisateurs abonné par l'utilisateur
    public function getFollowedUserID(): array
    {
        $db = ConnectionFactory::makeConnection();
        $query = 'SELECT suiviID 
                  FROM suivi 
                  WHERE suivreID = :suiveur';
        $st = $db->prepare($query);
        $st->bindParam(":suiveur", $_SESSION['utilisateur']['userID']);
        $st->execute();
        $tabTagFollow = [];
        while ($data = $st->fetch()) {
            $tabTagFollow[] = $data['suiviID'];
        }
        return $tabTagFollow;
    }

    // Récupère l'id des touites des follows de l'utilisateur
    public function getTouitIDFromFollowedUsers(): array
    {
        $tabFollow = $this->getFollowedUserID();
        $tabTouitFollow = [];

        $db = ConnectionFactory::makeConnection();
        foreach ($tabFollow as $follow) {
            $query = "SELECT TouiteID  
                      FROM touitesutilisateurs
                      WHERE utilisateurID = :followID";

            $st = $db->prepare($query);
            $st->bindParam(":followID", $follow);
            $st->execute();
            while ($data = $st->fetch()) {
                $tabTouitFollow[] = $data['TouiteID'];
            }
        }
        return $tabTouitFollow;
    }

    // Récupère les touites de de follows de l'utilisateur
    public function getTouitsFromFollowedUsers(): string
    {
        $tab_touites_follow = $this->getTouitIDFromFollowedUsers();
        $touits_follow = "";

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

            $touits_follow .= $this->generateTweetHTML($st);
        }
        return $touits_follow;
    }


    /***************************************************************************
     *                                                                         *
     * FONCTIONS POUR RECUPERER LES TOUITES CONTENANT LES TAGS OU IL EST ABONNE *
     *                                                                         *
     ***************************************************************************/
    // Récupère les tag follow par l'utilisateur
    public function getFollowedTagID(): array
    {
        $db = ConnectionFactory::makeConnection();
        $query = 'SELECT tagID 
                  FROM abonnementtags 
                  WHERE utilisateurID = :utilisateurID';
        $st = $db->prepare($query);
        $st->bindParam(":utilisateurID", $_SESSION['utilisateur']['userID']);
        $st->execute();
        $tabFollow = [];
        while ($data = $st->fetch()) {
            $tabFollow[] = $data['tagID'];
        }

        return $tabFollow;
    }

    // Récupère l'id des touites des tag follows par l'utilisateur
    public function getTouitIDFromFollowedTag(): array
    {
        $tabTagFollow = $this->getFollowedTagID();
        $tabTouitIdTagFollow = [];

        $db = ConnectionFactory::makeConnection();
        foreach ($tabTagFollow as $tag_follow) {
            $query = "SELECT TouiteID  
                      FROM touitestags
                      WHERE tagID = :followTagID";

            $st = $db->prepare($query);
            $st->bindParam(":followTagID", $tag_follow);
            $st->execute();
            while ($data = $st->fetch()) {
                $tabTouitIdTagFollow[] = $data['TouiteID'];
            }
        }
        return $tabTouitIdTagFollow;
    }

    // Récupère les touites de de follows de l'utilisateur
    public function getTouitTagFollow(): string
    {
        $tab_tag_touites_follow = $this->getTouitIDFromFollowedTag();
        $touits_tag_follow = "";

        $db = ConnectionFactory::makeConnection();
        foreach ($tab_tag_touites_follow as $touit_tag) {
            $query = "SELECT t.touiteID, t.texte, u.utilisateurID, u.nom, u.prenom, t.datePublication, Tags.tagID, Tags.libelle
                      FROM Touites t
                      LEFT JOIN TouitesUtilisateurs ON t.touiteID = TouitesUtilisateurs.TouiteID
                      LEFT JOIN Utilisateurs u ON TouitesUtilisateurs.utilisateurID = u.utilisateurID
                      LEFT JOIN TouitesImages ON TouitesImages.TouiteID= t.TouiteID
                      LEFT JOIN Images ON Images.ImageID=TouitesImages.ImageID
                      LEFT JOIN TouitesTags ON t.touiteID = TouitesTags.TouiteID
                      LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                      WHERE t.touiteID = :touit_tag
                     ORDER BY t.datePublication DESC";
            $st = $db->prepare($query);
            $st->bindParam(':touit_tag', $touit_tag);

            $touits_tag_follow .= $this->generateTweetHTML($st);
        }
        return $touits_tag_follow;
    }

    // Fonction pour générer le code HTML d'un touit
    public function generateTweetHTML(\PDOStatement $st): string
    {
        $touits_followed = "";
        if ($st->execute()) {
            while ($data = $st->fetch()) {
                $touiteID = $data['touiteID'];
                $userID = $data['utilisateurID'];
                $userName = $data['prenom'] . ' ' . $data['nom'];
                $tagID = $data['tagID'];
                $libelle = $data['libelle'];
                $timestamp = $data['datePublication'];

                // Touit court
                $touits_followed .= <<<HTML
                                <div class="menu-nav">
                                    <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$userID'>$userName</a></div>
                                    <div class="timestamp">Publié le : $timestamp</div>
                                    <a class="details-link" href="?action=details&tweet_id=$touiteID">Voir les détails</a>
                                </div>
                                HTML;
            }
        }
        return $touits_followed;
    }

    // GETTERS
    public function getPseudo(): string
    {
        return $this->pseudo;
    }
}
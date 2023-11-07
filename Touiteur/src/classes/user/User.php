<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\db\ConnectionFactory;

class User{
    private string $pseudo;
    private string $email;
    private string $mdp;

    // Constructeur
    public function __construct(string $nom, string $prenom, string $email, string $mdp){
        $this->pseudo = $prenom . "_" . $nom;
        $this->email = $email;
        $this->mdp = $mdp;
    }

    // Récupère les follows de l'utilisateur
    public function getFollower() :array{
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

    public function getTouitFromFollow(): array {

        $tabTouitFollow = [];
        return $tabTouitFollow;
    }

    // GETTERS
    public function getPseudo(): string {
        return $this->pseudo;
    }
}
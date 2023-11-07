<?php

namespace iutnc\touiteur\user;

class User{
    private string $pseudo;
    private string $email;
    private string $mdp;

    public function __construct(string $nom, string $prenom, string $email, string $mdp){
        $this->pseudo = $prenom . "_" . $nom;
        $this->email = $email;
        $this->mdp = $mdp;
    }
}
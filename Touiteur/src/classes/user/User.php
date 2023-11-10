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
}
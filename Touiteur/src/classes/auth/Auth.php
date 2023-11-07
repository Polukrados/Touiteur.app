<?php

namespace iutnc\touiteur\auth;

use iutnc\touiteur\db;

class Auth
{

    public static function authenticate(string $email, string $passwd): bool
    {
        $db = db\ConnectionFactory::makeConnection();
        $query = 'SELECT * FROM utilisateur WHERE email = :email';
        $st = $db->prepare($query);
        $st->bindParam(':email', $email);
        $st->execute();
        $userData = $st->fetch();

        if (!$userData) {
            return false;
        }

        if (password_verify($passwd, $userData['passwd'])) {
            $_SESSION['user'] = [
                'id' => $userData['id'],
                'email' => $userData['email'],
                'role' => $userData['role'],
            ];
            return true;
        }
        return false;
    }


    public function register(string $email, string $mdp, int $role): bool
    {
        // vérifie la validité de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

        // vérifie que l'utilisateur n'existe pas déjà
        $db = db\ConnectionFactory::makeConnection();
        $query = "SELECT email FROM utilisateur where email = :email";
        $st = $db->prepare($query);
        $st->bindParam(":email", $email);
        $st->execute();
        $data = $st->fetch();

        if ($data) {
            return false;
        }

        // vérifie la qualité du mdp l'ajoute à la bd s'il est bon
        if ($this->checkPasswordStrength($mdp, 10)) {
            $hash = password_hash($mdp, PASSWORD_DEFAULT, ['cost' => 12]);
            $query = "INSERT INTO utilisateur (email, passwd) VALUES(:email, :hash)";
            $st = $db->prepare($query);
            $st->bindParam(":email", $email);
            $st->bindParam(":hash", $hash);
            $st->execute();

            return true;
        }
        return false;
    }

    public function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        return (strlen($pass) >= $minimumLength);
    }
}

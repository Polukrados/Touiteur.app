<?php

namespace iutnc\admin\auth;

use iutnc\admin\db;

class AuthAdmin
{
    public static function authenticate(string $email, string $mdp): bool
    {
        $db = db\ConnectionFactoryAdmin::makeConnection();
        $query = 'SELECT * FROM Utilisateurs WHERE email = :email';
        $st = $db->prepare($query);
        $st->bindParam(':email', $email);
        $st->execute();
        $userData = $st->fetch();

        if (!$userData) {
            return false;
        }

        if (password_verify($mdp, $userData['mdp'])) {
            $_SESSION['utilisateur'] = [
                'userID' => $userData['utilisateurID'],
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'email' => $userData['email'],
                'mdp' => $userData['mdp']
            ];
            return true;
        }
        return false;
    }
}
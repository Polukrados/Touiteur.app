<?php

namespace iutnc\admin\auth;

use iutnc\admin\db;

/**
 * Classe gérant l'authentification de l'admin.
 */
class AuthAdmin
{
    // fonction pour se connecter
    public static function authenticate(string $email, string $mdp): bool
    {
        $db = db\ConnectionFactoryAdmin::makeConnection();
        // requête pour récupérer les données de l'utilisateur
        $query = 'SELECT * FROM utilisateurs WHERE email = :email';
        $st = $db->prepare($query);
        $st->bindParam(':email', $email);
        $st->execute();
        $userData = $st->fetch();

        // si l'utilisateur n'existe pas return false
        if (!$userData) {
            return false;
        }

        // si le mot de passe est correct on crée la session et return true sinon return false
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
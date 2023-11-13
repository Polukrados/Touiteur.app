<?php

namespace iutnc\touiteur\auth;

use iutnc\touiteur\db;
use iutnc\touiteur\exception\LoginException;

/**
 * Action permettant de verifier les connexion et inscription
 */
class Auth
{
    // fonction pour se connecter
    public static function authenticate(string $email, string $mdp): bool
    {
        $db = db\ConnectionFactory::makeConnection();
        $query = 'SELECT * FROM utilisateurs WHERE email = :email';
        $st = $db->prepare($query);
        $st->bindParam(':email', $email);
        $st->execute();
        $userData = $st->fetch();

        if (!$userData) {
            return false;
        }

        // verifie le mdp et s'il est bon alors return true
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

    // fonction pour s'inscrire
    public function register(string $nom, string $prenom, string $email, string $mdp): bool
    {
        $nom = filter_var($nom, FILTER_SANITIZE_SPECIAL_CHARS);
        $prenom = filter_var($prenom, FILTER_SANITIZE_SPECIAL_CHARS);
        // vérifie la validité de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

        // vérifie que l'utilisateur n'existe pas déjà
        $db = db\ConnectionFactory::makeConnection();
        $query = "SELECT * FROM utilisateurs where nom = :nom and prenom = :prenom and email = :email";
        $st = $db->prepare($query);
        $st->bindParam(":nom", $nom);
        $st->bindParam(":prenom", $prenom);
        $st->bindParam(":email", $email);
        $st->execute();
        $data = $st->fetch();

        // si une donnée est trouvé alors l'utilisateur à déjà un compte
        if ($data) {
            return false;
        }

        // vérifie la qualité du mdp et l'ajoute à la bd s'il est bon
        if ($this->checkPasswordStrength($mdp, 8)) {
            $hash = password_hash($mdp, PASSWORD_DEFAULT, ['cost' => 12]);
            $query2 = "INSERT INTO utilisateurs (nom, prenom, email, mdp) VALUES(:nom, :prenom, :email, :hash)";
            $st = $db->prepare($query2);
            $st->bindParam(":nom", $nom);
            $st->bindParam(":prenom", $prenom);
            $st->bindParam(":email", $email);
            $st->bindParam(":hash", $hash);
            $st->execute();

            $userData = $st->fetch();

            $_SESSION['utilisateur'] = [
                'userID' => $db->lastInsertId(),
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mdp' => $hash
            ];

            return true;
        }
        return false;
    }

    // fonction qui vérifie la longueur du mdp
    public function checkPasswordStrength(string $pass, int $minimumLength): bool
    {
        return (strlen($pass) >= $minimumLength);
    }
}

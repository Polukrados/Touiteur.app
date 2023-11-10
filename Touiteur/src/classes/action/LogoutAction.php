<?php

namespace iutnc\touiteur\action;

/**
 * Action permettant de se déconnecter
 */
class LogoutAction extends Action
{

    // Méthode qui exécute l'action
    public function execute(): string
    {
        $add_user = "";
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
        if ($this->http_method == "GET") {
            session_destroy();
            header("Location: ?action=default");
            exit;
        }
        return $add_user;
    }
}
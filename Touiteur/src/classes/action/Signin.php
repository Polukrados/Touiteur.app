<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\exception\AuthException;
use iutnc\touiteur\exception\LoginException;
use iutnc\touiteur\exception\RegisterException;
use iutnc\touiteur\user\User;

class Signin extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $signin = "";
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $signin .= <<<HTML
                <header> 
                  <p class ='libelle_page_courante'>Connexion</p> 
                  <nav class="menu">
                    <ul>
                      <li><a href="?action=accueil">Accueil</a></li>
                      <li><a href="?action=add-user">S'inscrire</a></li>
                    </ul>
                  </nav>
                </header>
                      <div class="password">
                            <form class="login_form" action='?action=signin' method='post'>
                                            <h1> Connecter-vous à Touiteur </h1>
                            <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                            <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                            <input type='submit' value='Se connecter'>
                      </div>
              HTML;
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Valeurs de l'utilisateur
            $email = $_POST["email"];
            $mdp = $_POST["password"];

            // vérifie si le compte existe
            if (Auth::authenticate($email, $mdp)) {
                $user = new User($_SESSION['utilisateur']['nom'], $_SESSION['utilisateur']['prenom'], $_SESSION['utilisateur']['email'], $_SESSION['utilisateur']['mdp']);
                $pseudo = $user->getPseudo();
                $signin .= <<<HTML
                            <header> 
                              <p class ='libelle_page_courante'>$pseudo</p> 
                              <nav class="menu">
                                <ul>
                                </ul>
                              </nav>
                            </header>
                            HTML;
            } else {
                echo "signin";
                throw new LoginException();
            }
        }
        return $signin;
    }
}
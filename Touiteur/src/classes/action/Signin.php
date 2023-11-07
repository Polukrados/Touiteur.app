<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\exception\AuthException;

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
            $email = $_POST["email"];
            $mdp = $_POST["password"];

            try {
                if (Auth::authenticate($email, $mdp)){

                }
            } catch (AuthException $e){
                $e->getMessage();
            }
        }
        return $signin;
    }
}
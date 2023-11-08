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
                  <nav class="menu-nav">
                    <ul>
                      <li><a href="?action=accueil">Accueil</a></li>
                      <li><a href="?action=add-user">S'inscrire</a></li>
                    </ul>
                  </nav>
                </header>
                      <div class="form-container">
                            <form class="form" action='?action=signin' method='post'>
                                            <h1> Connectez-vous à Touiteur </h1>
                            <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                            <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                            <input type='submit' value='Se connecter'>
                      </div>
              HTML;
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Valeurs de l'utilisateur
            $email = $_POST["email"];
            $mdp = $_POST["password"];

            // vérifie si le compte existe et si c'est le cas, affiche les touites de ses abonnements
            if (Auth::authenticate($email, $mdp)) {
                $user = new User(intval($_SESSION['utilisateur']['userID']), $_SESSION['utilisateur']['nom'], $_SESSION['utilisateur']['prenom'], $_SESSION['utilisateur']['email'], $_SESSION['utilisateur']['mdp']);
                $pseudo = $user->getPseudo();
                $tweets = $user->getTouitFollow();
                $signin .= <<<HTML
                            <header> 
                              <p class ='libelle_page_courante'>$pseudo</p> 
                              <nav class="menu-nav">
                                <ul>
                                  <li><a href="?action=post-touite">Publier un touite</a></li>
                                </ul>
                              </nav>
                            </header>
                            <div class="tweets">
                                $tweets
                            </div>                     
                          HTML;

            } else { // Affiche une erreur si la connexion a échoué
                $signin .= <<<HTML
                            <header> 
                              <p class ='libelle_page_courante'>Connexion</p> 
                              <nav class="menu-nav">
                                <ul>
                                  <li><a href="?action=accueil">Accueil</a></li>
                                  <li><a href="?action=add-user">S'inscrire</a></li>
                                </ul>
                              </nav>
                            </header>
                            <div class="form-container">
                                <form class="form" action='?action=signin' method='post'>
                                                    <h1> Connecter-vous à Touiteur </h1>
                                <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                                <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                                <input type='submit' value='Se connecter'>
                                <p class="erreur_authentification"> Connexion échouée </p>
                          </div>                      
                          HTML;
            }

        }
        return $signin;
    }
}
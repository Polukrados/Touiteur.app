<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;

/**
 * Action permettant de se connecter ou de s'incrire
 */
class Signin extends Action
{
    // Constructeur
    public function __construct()
    {
        parent::__construct();
    }

    // Methode qui execute l'action
    public function execute(): string
    {
        $signin = "";
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $signin .= <<<HTML
                <header> 
                  <p class ='libelle_page_courante'>Connexion</p> 
                  <nav class="menu-nav">
                    <ul>
                      <li><a href="?action=accueil"><i class="fa fa-home"></i></a></li>
                      <li><a href="?action=add-user">S'inscrire</a></li>
                    </ul>
                  </nav>
                </header>
                      <div class="form-container">
                            <form class="form" action='?action=signin' method='post'>
                                            <h1> Connectez-vous à Touiteur </h1>
                            <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                            <div class="password-container">
                                <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required>
                                <i class="toggle-password fa fa-eye-slash" onclick="togglePasswordVisibility()"></i>
                            </div>
                            <br><br>
                            <input type='submit' value='Se connecter'>
                      </div>
              HTML;
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $mdp = $_POST["password"];

            // vérifie si le compte existe et si c'est le cas, affiche les touites de ses abonnements
            if (Auth::authenticate($email, $mdp)) {
                $userID = $_SESSION['utilisateur']['userID'];
                header("Location: ?action=default&user_id=$userID");
                exit();
            } else { // Affiche une erreur si la connexion a échoué
                $signin .= <<<HTML
                            <header> 
                              <p class ='libelle_page_courante'>Connexion</p> 
                              <nav class="menu-nav">
                                <ul>
                                  <li><a href="?action=accueil"><i class="fa fa-home"></i></a></li>
                                  <li><a href="?action=add-user">S'inscrire</a></li>
                                </ul>
                              </nav>
                            </header>
                            <div class="form-container">
                                <form class="form" action='?action=signin' method='post'>
                                                    <h1> Connectez-vous à Touiteur </h1>
                                <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                                <div class="password-container">
                                    <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required>
                                    <i class="toggle-password fa fa-eye-slash" onclick="togglePasswordVisibility()"></i>
                                </div>
                                <br><br>
                                <input type='submit' value='Se connecter'>
                                <p class="erreur_authentification"> Connexion échouée </p>
                          </div>                      
                          HTML;
            }

        }
        return $signin;
    }
}
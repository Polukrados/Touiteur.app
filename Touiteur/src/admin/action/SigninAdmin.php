<?php

namespace iutnc\admin\action;

use iutnc\admin\auth\AuthAdmin;

class SigninAdmin extends ActionAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $signin = "";
        if ($this->http_method === 'GET') {
            $signin .= <<<HTML
                <header> 
                  <p class ='libelle_page_courante'>Connexion Admin</p> 
                    <nav class="menu">
                    <ul>
                        <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                    </ul>
                    </nav>
                </header>
                      <div class="form-container">
                            <form class="form" action='?action=defaultadmin' method='post'>
                            <h1> Connectez-vous au back-office </h1>
                            <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                            <div class="password-container">
                                <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required>
                                <i class="toggle-password fa fa-eye-slash" onclick="togglePasswordVisibility()"></i>
                            </div>
                            <br><br>
                            <input type='submit' value='Se connecter'>
                      </div>
              HTML;
        } else {
            $email = $_POST["email"];
            $mdp = $_POST["password"];
            if (AuthAdmin::authenticate($email, $mdp)) {
                if ($_SESSION['utilisateur']['email'] == "root@gmail.com" && password_verify('rootrootroot', $_SESSION['utilisateur']['mdp'])) {
                    header("Location: ?action=defaultadmin");
                    exit();
                }
            } else {
                $signin .= <<<HTML
                                                <header> 
                                                <p class ='libelle_page_courante'>Connexion Admin</p> 
                                                <nav class="menu-nav">
                                                </nav>
                                                </header>
                                                <div class="form-container">
                                                <form class="form" action='?action=defaultadmin' method='post'>
                                                <h1> Connectez-vous au back-office </h1>
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
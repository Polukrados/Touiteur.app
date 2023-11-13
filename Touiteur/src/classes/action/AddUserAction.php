<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;

/**
 * Action permettant d'ajouter un utilisateur / s'inscrire
 */
class AddUserAction extends Action
{

    // Méthode exécutant l'action
    public function execute(): string
    {
        $add_user = "";
        // Si l'utilisateur n'est pas connecté, on affiche le formulaire de connexion
        if ($this->http_method == "GET") {
            $add_user = <<<HTML
                  <header> 
                    <p class ='libelle_page_courante'>Rejoignez Touiteur dès maintenant !</p>
                    <nav class="menu-nav">
                      <ul>
                        <li><a href="?action=accueil"><i class="fa fa-home"></i></a></li>
                        <li><a href="?action=signin">Se connecter</a></li>
                      </ul>
                    </nav>
                  </header>
                  <div class="form-container">
                        <form class="form" action='?action=add-user' method='post'>
                                          <h1> Créer votre compte </h1>
                        <input type='text' placeholder='Nom' name='nom' id='nom' class='input-icon-nom' required><br><br>
                        <input type='text' placeholder='Prénom' name='prenom' id='prenom' class='input-icon-prenom' required><br><br>
                        <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                        <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                        <input type='submit' value="S'inscrire">
                  </div>
                  HTML;
            // Si l'utilisateur est connecté, on le redirige vers la page d'accueil
        } else if ($this->http_method == "POST") {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $passwd = $_POST['password'];

            $auth = new Auth();
            // Si l'inscription réussit, on redirige l'utilisateur vers la page d'accueil
            if ($auth->register($nom, $prenom, $email, $passwd)) {
                header('Location: ?action=default');
                exit;
            // Sinon, on affiche un message d'erreur
            } else {
                $add_user .= <<<HTML
                                <header> 
                                  <p class ='libelle_page_courante'>Connexion</p> 
                                  <nav class="menu-nav">
                                    <ul>
                                      <li><a href="?action=accueil">Accueil</a></li>
                                      <li><a href="?action=signin">Se connecter</a></li>
                                    </ul>
                                  </nav>
                                </header>
                                <div class="form-container">
                                    <form class="form" action='?action=add-user' method='post'>
                                                      <h1> Créer votre compte </h1>
                                    <input type='text' placeholder='Nom' name='nom' id='nom' class='input-icon-nom' required><br><br>
                                    <input type='text' placeholder='Prénom' name='prenom' id='prenom' class='input-icon-prenom' required><br><br>
                                    <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                                    <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                                    <input type='submit' value="S'inscrire">
                                    <p class="erreur_authentification"> Inscription échouée </p>
                              </div>                      
                              HTML;
            }
        }
        return $add_user;
    }
}
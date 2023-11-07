<?php

namespace iutnc\touiteur\exception;

class RegisterException extends \Exception
{

    public function __construct()
    {
        $html_debut = <<<HTML
                    <!DOCTYPE html>
                        <html lang="fr">
                        <head>
                          <meta charset="UTF-8">
                          <meta name="viewport" content="width=device-width, initial-scale=1.0">
                          <link rel="stylesheet" href="style.css">
                          <title>Touiteur</title>
                        </head>
                        <body>    
                  HTML;

        $erreur = <<<HTML
                    <header> 
                      <p class ='libelle_page_courante'>Connexion</p> 
                      <nav class="menu">
                        <ul>
                          <li><a href="?action=accueil">Accueil</a></li>
                          <li><a href="?action=signin">Se connecter</a></li>
                        </ul>
                      </nav>
                    </header>
                    <div class="add-user">
                        <form class="add_user_form" action='?action=add-user' method='post'>
                                          <h1> Créer votre compte </h1>
                        <input type='text' placeholder='Nom' name='nom' id='nom' class='input-icon-nom' required><br><br>
                        <input type='text' placeholder='Prénom' name='prenom' id='prenom' class='input-icon-prenom' required><br><br>
                        <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                        <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                        <input type='submit' value="S'inscrire">
                        <p class="erreur_authentification"> Inscription échouée </p>
                  </div>                      
                  HTML;

        $html_fin = <<<HTML
                  </body>
                  </html>
                HTML;

        $html_complet = $html_debut . $erreur . $html_fin;
        parent::__construct($html_complet);
    }
}
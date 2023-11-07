<?php

namespace iutnc\touiteur\exception;

class LoginException extends \Exception
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
                        <p class="erreur_authentification"> Connexion échouée </p>
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
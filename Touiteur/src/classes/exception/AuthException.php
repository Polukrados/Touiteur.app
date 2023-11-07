<?php

namespace iutnc\touiteur\exception;

class AuthException extends \Exception
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
                <p class ='libelle_page_courante'>Inscription</p>
                <nav class="menu">
                  <ul>
                    <li><a href="?action=accueil">Accueil</a></li>
                    <li><a href="?action=add-user">S'inscrire</a></li>
                  </ul>
                </nav>
              </header>
              <h1>Le mot de passe ne correspond pas</h1>
              HTML;

        $html_fin = <<<HTML
                  </body>
                  </html>
                HTML;

        $html_complet = $html_debut . $erreur . $html_fin;
        parent::__construct($html_complet);
    }
}

<?php

namespace iutnc\touiteur\action;

class AddUserAction extends Action
{

    public function execute(): string
    {
        $add_user = "";
        if ($this->http_method == "GET") {
            $add_user .= <<<HTML
                  <header> 
                    <p class ='libelle_page_courante'>Rejoignez Touiteur dès maintenant !</p>
                    <nav class="menu">
                      <ul>
                        <li><a href="?action=accueil">Accueil</a></li>
                        <li><a href="?action=signin">Se connecter</a></li>
                      </ul>
                    </nav>
                  </header>
                  <h1> Créer votre compte </h1>
                  <div class="add-user">
                        <form class="add_user_form" action='?action=add-user' method='post'>
                        <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                        <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                        <input type='number' placeholder='Age' name='age' id='age' class='input-icon-age' required><br><br>
                        <input type='submit' value='Connexion'>
                  </div>
                  HTML;
        } else if ($this->http_method == "POST") {
            // valeurs
            $email = $_POST['email'];
            $passwd = $_POST['password'];
        }

        return $add_user;
    }
}
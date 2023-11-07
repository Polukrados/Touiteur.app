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
                  <div class="add-user">
                        <form class="add_user_form" action='?action=add-user' method='post'>
                                          <h1> Créer votre compte </h1>
                        <input type='text' placeholder='Nom' name='nom' id='nom' class='input-icon-nom' required><br><br>
                        <input type='text' placeholder='Prénom' name='prenom' id='prenom' class='input-icon-prenom' required><br><br>
                        <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                        <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                        <input type='submit' value='S'inscrire>
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
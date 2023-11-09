<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\user\User;

class DisplayAbonnementTouitesAction extends Action
{

    public function execute(): string
    {
        $signin = "";
        if ($this->http_method == "GET"){
            $user = new User(intval($_SESSION['utilisateur']['userID']), $_SESSION['utilisateur']['nom'], $_SESSION['utilisateur']['prenom'], $_SESSION['utilisateur']['email'], $_SESSION['utilisateur']['mdp']);
            $userID = $_SESSION['utilisateur']['userID'];
            $pseudo = $user->getPseudo();
            $touits = $user->getTouitsFromFollowedUsers();
            $touits .= $user->getTouitTagFollow();
            $signin .= <<<HTML
                            <header> 
                              <p class ='libelle_page_courante'>$pseudo</p> 
                              <nav class="menu-nav">
                                <ul>
                                  <li><a href="?action=profile-user&user_id=$userID">Mon profil</a></li>
                                  <li><a href="?action=post-touite">Publier un touite</a></li>
                                  <li><a href="?action=default"><i class="fa-solid fa-house"></i></a></li>
                                </ul>
                              </nav>
                              <nav class="menu">
                                <div class="photo-profil">
                                    <a href="#lien_vers_profil_peut_etre_pas_oblige">
                                        <img src="images/gaetan.png" alt="Icône de profil">
                                    </a>
                                </div>
                                <ul>
                                    <li><a href="?action=logout">Déconnexion</a></li>
                                <ul>
                              </nav>
                            </header>
                            <div class="tweets">
                                $touits
                            </div>
                          HTML;
        }
        return $signin;
    }
}
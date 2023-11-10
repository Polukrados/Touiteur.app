<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\user\User;
use iutnc\Touiteur\db\ConnectionFactory;
use PDO;

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
                $user = new User(intval($_SESSION['utilisateur']['userID']), $_SESSION['utilisateur']['nom'], $_SESSION['utilisateur']['prenom'], $_SESSION['utilisateur']['email'], $_SESSION['utilisateur']['mdp']);
                $pseudo = $user->getPseudo();

                $listtouites = 'Liste des touites des utilisateurs que '. $pseudo. ' suit :<br><br>';
                $touits = $user->getTouitsFromFollowedUsers();

                // Si l'utilisateur recherche et s'abonne à un tag
                if (isset($_POST['f'])) {
                    $tagToFollow = htmlspecialchars($_POST['f']);
                    $this->followTag($tagToFollow);
                }

                $listtags = 'Liste des tweets contenant un tag que '.$pseudo. ' suit :<br><br>';
                $touitstags = $user->getTouitTagFollow();

                if (isset($_POST['u'])) {
                    $tagToUnfollow = htmlspecialchars($_POST['u']);
                    $this->unfollowTag($tagToUnfollow);
                }

                $signin .= <<<HTML
                            <header>
                              <p class ='libelle_page_courante'>$pseudo</p>
                              <nav class="menu-nav">
                                <ul>
                                  <li><a href="?action=post-touite">Publier un touite</a></li>
                                  <li><a href="?action=accueil"><i class="fa fa-home"></i></a></li>
                                </ul>
                              </nav>
                            </header>
                            <div class="tweets">
                                $listtouites
                                $touits
                                <br>
                                <form class="form" action="?action=signin" method="post">
                                    <input type="hidden" name="email" value="$email">
                                    <input type="hidden" name="password" value="$mdp">
                                    <input type="text" placeholder="S'abonner à un tag" name="f" id="tagSearch" class="input-icon-email">
                                    <input type="submit" value="S'abonner">
                                </form>
                                <br>
                                <form class="form" action="?action=signin" method="post">
                                    <input type="hidden" name="email" value="$email">
                                    <input type="hidden" name="password" value="$mdp">
                                    <input type="text" placeholder="Se désabonner d'un tag" name="u" id="tagSearch" class="input-icon-email">
                                    <input type="submit" value="Se désabonner">
                                </form>
                                <br>
                                $listtags
                                $touitstags
                            </div>
                          HTML;

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
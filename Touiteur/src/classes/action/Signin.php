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
                      <li><a href="?action=accueil">Accueil</a></li>
                      <li><a href="?action=add-user">S'inscrire</a></li>
                    </ul>
                  </nav>
                </header>
                      <div class="form-container">
                            <form class="form" action='?action=signin' method='post'>
                                            <h1> Connectez-vous à Touiteur </h1>
                            <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                            <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                            <input type='submit' value='Se connecter'>
                      </div>
              HTML;
        } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $mdp = $_POST["password"];

            // vérifie si le compte existe et si c'est le cas, affiche les touites de ses abonnements
            if (Auth::authenticate($email, $mdp)) {
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
                                  <li><a href="?action=accueil">Accueil</a></li>
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
                                  <li><a href="?action=accueil">Accueil</a></li>
                                  <li><a href="?action=add-user">S'inscrire</a></li>
                                </ul>
                              </nav>
                            </header>
                            <div class="form-container">
                                <form class="form" action='?action=signin' method='post'>
                                                    <h1> Connectez-vous à Touiteur </h1>
                                <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                                <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required><br><br>
                                <input type='submit' value='Se connecter'>
                                <p class="erreur_authentification"> Connexion échouée </p>
                          </div>                      
                          HTML;
            }

        }
        return $signin;
    }

    private function followTag($tag)
    {
        // Effectuez l'insertion dans la table Abonnementtags
        $db = ConnectionFactory::makeConnection();

        $tagIDQuery = $db->prepare("SELECT tagID FROM Tags WHERE libelle = :tag");
        $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
        $tagIDQuery->execute();

        $tagID = $tagIDQuery->fetchColumn();

        if ($tagID !== false) {
            $checkQuery = $db->prepare("SELECT count(*) FROM Abonnementtags
                                    WHERE utilisateurID = :user AND tagID = :tag");
            $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
            $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
            $checkQuery->execute();
            $check = $checkQuery->fetchColumn();
            if($check==0) {
                $followQuery = $db->prepare("INSERT INTO Abonnementtags(utilisateurID, tagID) VALUES(:user, :tag)");
                $followQuery->bindValue(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                $followQuery->bindValue(':tag', $tagID, PDO::PARAM_INT);
                $followQuery->execute();
            }
        }
    }

    // Récupère les tag follow par l'utilisateur
        private function unfollowTag($tag)
        {
        // Effectuez l'insertion dans la table Abonnementtags
            $db = ConnectionFactory::makeConnection();

            $tagIDQuery = $db->prepare("SELECT tagID FROM Tags WHERE libelle = :tag");
            $tagIDQuery->bindParam(':tag', $tag, PDO::PARAM_STR);
            $tagIDQuery->execute();

            $tagID = $tagIDQuery->fetchColumn();

            if ($tagID !== false) {
                        $checkQuery = $db->prepare("SELECT count(*) FROM Abonnementtags
                                                WHERE utilisateurID = :user AND tagID = :tag");
                        $checkQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                        $checkQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
                        $checkQuery->execute();

                        $check = $checkQuery->fetchColumn();
                        if($check!==0) {
                            $deleteQuery = $db->prepare("DELETE FROM Abonnementtags WHERE utilisateurID = :user AND tagID = :tag");
                            $deleteQuery->bindParam(':user', $_SESSION['utilisateur']['userID'], PDO::PARAM_INT);
                            $deleteQuery->bindParam(':tag', $tagID, PDO::PARAM_INT);
                            $deleteQuery->execute();
                        }
            }
        }
}
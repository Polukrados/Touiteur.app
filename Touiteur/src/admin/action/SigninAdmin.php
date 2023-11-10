<?php

namespace iutnc\admin\action;

use iutnc\admin\auth\AuthAdmin;

/**
 * Classe gérant la connexion à l'interface d'administration.
 */
class SigninAdmin extends ActionAdmin
{
    /**
     * Constructeur de la classe SigninAdmin.
     * Appelle le constructeur de la classe parente ActionAdmin.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exécute l'action de connexion pour l'admin.
     *
     * @return string Le HTML généré pour la page de connexion.
     */
    public function execute(): string
    {
        $signin = "";

        // Traitement de la requête GET pour afficher le formulaire de connexion.
        if ($this->http_method === 'GET') {
            // Concaténation du HTML pour le formulaire de connexion dans la variable $signin.
            $signin .= <<<HTML
                <!-- Entête de la page avec le libellé de la page courante -->
                <header>
                  <p class ='libelle_page_courante'>Connexion Admin</p> 
                    <!-- Navigation avec un bouton pour publier un touite -->
                    <nav class="menu">
                    <ul>
                        <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                    </ul>
                    </nav>
                </header>
                <!-- Conteneur du formulaire de connexion -->
                <div class="form-container">
                    <!-- Formulaire de connexion -->
                    <form class="form" action='?action=defaultadmin' method='post'>
                        <h1> Connectez-vous au back-office </h1>
                        <input type='email' placeholder='Email' name='email' id='email' class='input-icon-email' required><br><br>
                        <div class="password-container">
                            <input type='password' placeholder='Mot de passe' name='password' id='password' class='input-icon-password' required>
                            <!-- Icône permettant de basculer la visibilité du mot de passe -->
                            <i class="toggle-password fa fa-eye-slash" onclick="togglePasswordVisibility()"></i>
                        </div>
                        <br><br>
                        <input type='submit' value='Se connecter'>
                </div>
            HTML;
        } else {
            // Traitement de la requête POST pour effectuer la connexion.
            $email = $_POST["email"];
            $mdp = $_POST["password"];

            // Tentative d'authentification avec les identifiants fournis.
            if (AuthAdmin::authenticate($email, $mdp)) {
                // Vérification si l'utilisateur est l'administrateur root.
                if ($_SESSION['utilisateur']['email'] == "root@gmail.com" && password_verify('rootrootroot', $_SESSION['utilisateur']['mdp'])) {
                    // Redirection vers la page par défaut de l'admin après l'authentification réussie.
                    header("Location: ?action=defaultadmin");
                    exit();
                }
            } else {
                // Génère le HTML pour afficher un message d'erreur si l'authentification échoue.
                $signin .= <<<HTML
                    <!-- Affichage du message d'erreur si les identifiants sont incorrects -->
                    <p class="erreur_authentification"> Connexion échouée </p>
                HTML;
            }
        }
        // Retourne le HTML généré pour la page de connexion.
        return $signin;
    }
}
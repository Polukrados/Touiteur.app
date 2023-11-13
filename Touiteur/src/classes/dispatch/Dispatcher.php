<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action as action;

/**
 * Classe qui creer les actions
 * Permet d'afficher les pages
 */
class Dispatcher
{
    protected ?string $action = null;

    // Constructeur
    public function __construct()
    {
        if (isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }
    }

    // Methode qui lance les actions
    public function run(): void
    {
        // selon l'action on en creer une nouvelle
        switch ($this->action) {
            case 'add-user':
                $requete = new action\AddUserAction();
                break;
            case 'signin':
                $requete = new action\Signin();
                break;
            case 'user-touite-list':
                $requete = new action\UserTouiteListAction();
                break;
            case 'tag-touite-list':
                $requete = new action\TagTouiteListAction();
                break;
            case 'details':
                $requete = new action\DetailsAction();
                break;
            case 'post-touite':
                $requete = new action\PostTouiteAction();
                break;
            case 'profile-user':
                $requete = new action\ProfileUserAction();
                break;
            case 'logout':
                $requete = new action\LogoutAction();
                break;
            case 'display-abo':
                $requete = new action\DisplayAbonnementtouitesAction();
                break;
            case 'delete':
                $requete = new action\DeleteTouiteAction();
                break;
            // action par default donc l'accueil
            default:
                $requete = new action\DefaultAction();
                break;
        }
        $this->renderPage($requete->execute());
    }

    private function renderPage(string $content): void
    {
        // Contenu HTML du début
        $html_debut = <<<HTML
                  <!DOCTYPE html>
                  <html lang="fr">
                  <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="style.css">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
                    <script src="script.js"></script>
                    <title>Touiteur</title>
                  </head>
                  <body>
                  HTML;

        $html_fin = <<<HTML
                </body>
                </html>
                HTML;

        $html_complet = $html_debut . $content . $html_fin;
        echo $html_complet;
    }
}

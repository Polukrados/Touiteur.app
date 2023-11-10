<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action as action;

class Dispatcher
{
    protected ?string $action = null;

    public function __construct()
    {
        if (isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }
    }

    public function run(): void
    {
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
                $requete = new action\DisplayAbonnementTouitesAction();
                break;
            case 'follow-tag':
                $requete = new action\FollowTagAction();
                break;
            case 'unfollow-tag':
                $requete = new action\UnfollowTagAction();
                break;
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

        // Contenu HTML de fin
        $html_fin = <<<HTML
                </body>
                </html>
                HTML;

        // Contenu HTML complet
        $html_complet = $html_debut . $content . $html_fin;
        echo $html_complet;
    }
}

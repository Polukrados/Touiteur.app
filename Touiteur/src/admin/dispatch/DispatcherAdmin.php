<?php

namespace iutnc\admin\dispatch;

use iutnc\admin\action as action;

class DispatcherAdmin
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
            case 'defaultadmin':
                $requete = new action\AccueilAdmin();
                break;
            case 'influenceursadmin':
                $requete=new action\InfluenceurAdmin();
                break;
            case 'tendancesadmin':
                $requete=new action\TendanceAdmin();
                break;
            default:
                $requete = new action\SigninAdmin();
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

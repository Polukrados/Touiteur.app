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
            case 'display-touit':
                $requete = new action\DisplayTouit();
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

<?php

namespace iutnc\admin\dispatch;

use iutnc\admin\action as action;

/**
 * Classe permettant de dispatcher les requêtes
 * vers les bonnes classes d'action
 */
class DispatcherAdmin
{
    protected ?string $action = null;

    // Constructeur
    public function __construct()
    {
        // Récupération de l'action à effectuer
        if (isset($_GET["action"])) {
            $this->action = $_GET["action"];
        }
    }

    // Méthode principale
    public function run(): void
    {
        // Exécution de l'action selon la requête
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

    // Méthode permettant de générer la page HTML
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

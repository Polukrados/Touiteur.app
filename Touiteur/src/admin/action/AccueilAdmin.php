<?php

namespace iutnc\admin\action;

/**
 * Classe gérant l'affichage de la page d'accueil.
 */
class AccueilAdmin extends ActionAdmin
{
    // Constructeur
    public function __construct()
    {
        parent::__construct();
    }

    // Méthode d'exécution
    public function execute(): string
    {
        // Génération du contenu de la page d'accueil
        // on affiche les influenceurs et les tendances
        if ($this->http_method === 'GET') {
            $pageContent = <<<HTML
                <header> 
                  <p class ='libelle_page_courante'>Accueil Admin</p> 
                  <nav class="menu-nav">
                  </nav>
                </header>
                      <div class="form-container">
                            <a href="?action=influenceursadmin" class="btn-influenceurs">Voir les Influenceurs</a>

                            <a href="?action=tendancesadmin" class="btn-tendances">Voir les Tendances</a>
                        </div>
              HTML;
        } else {
            $pageContent = <<<HTML
                <header> 
                  <p class ='libelle_page_courante'>Accueil Admin</p> 
                  <nav class="menu-nav">
                  </nav>
                </header>
                      <div class="form-container">
                            <a href="?action=influenceursadmin" class="btn-influenceurs">Voir les Influenceurs</a>

                            <a href="?action=tendancesadmin" class="btn-tendances">Voir les Tendances</a>
                        </div>
              HTML;
        }
        return $pageContent;
    }
}
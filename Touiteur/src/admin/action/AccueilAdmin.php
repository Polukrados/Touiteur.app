<?php

namespace iutnc\admin\action;

class AccueilAdmin extends ActionAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
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
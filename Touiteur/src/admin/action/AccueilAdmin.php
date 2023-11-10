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
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $pageContent .= <<<HTML
                <header> 
                  <p class ='libelle_page_courante'>Acuueil Admin</p> 
                  <nav class="menu-nav">
                  </nav>
                </header>
                      <div class="buttons-container">
                            <a href="admin.php?action=influenceur" class="btn-influenceurs">Voir les Influenceurs</a>

                            <a href="admin.php?action=tendance" class="btn-tendances">Voir les Tendances</a>
                        </div>
              HTML;

        }
        return $pageContent;
    }
}
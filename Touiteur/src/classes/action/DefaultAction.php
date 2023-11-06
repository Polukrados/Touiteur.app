<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;

class DefaultAction extends Action
{

    private ArrayList<Touit> $list;

    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        return <<<HTML
            <header> 
              <p class ='libelle_page_courante'>Accueil</p> 
              <nav class="menu">
                 <ul>
                  
                 </ul>
               </nav>
            </header>
            <div class ='bienvenue-message'>Bienvenue sur Touiteur !</div>
           HTML;
    }
}

<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;

class DefaultAction extends Action
{

    private array $listTouit;

    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res="";
        $res.=<<<HTML
            <header> 
              <p class ='libelle_page_courante'>Accueil</p> 
              <nav class="menu">
                 <ul>
                  
                 </ul>
               </nav>
            </header>
           HTML;
        return $res;
    }
}

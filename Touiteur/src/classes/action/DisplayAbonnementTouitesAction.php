<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\user\User;

class DisplayAbonnementTouitesAction extends Action
{

    public function execute(): string
    {
        if ($this->http_method == "GET"){
            $pageContent = parent::generationAction("SELECT t.touiteID, t.texte, u.utilisateurID, u.nom, u.prenom, t.datePublication, Tags.tagID, Tags.libelle
FROM Touites t
LEFT JOIN TouitesUtilisateurs ON t.touiteID = TouitesUtilisateurs.TouiteID
LEFT JOIN Utilisateurs u ON TouitesUtilisateurs.utilisateurID = u.utilisateurID
LEFT JOIN TouitesTags ON t.touiteID = TouitesTags.TouiteID
LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
LEFT JOIN abonnementtags ON Tags.tagID = abonnementtags.tagID
LEFT JOIN suivi ON u.utilisateurID = suivi.suiviID
WHERE (abonnementtags.utilisateurID = :user_id1 OR suivi.suivreID = :user_id2)
ORDER BY t.datePublication DESC
LIMIT :limit OFFSET :offset;
",false,false,false,true);

        }
        return $pageContent;
    }
}
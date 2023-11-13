<?php

namespace iutnc\touiteur\action;

/**
 * Action permettant de voir les touites de l'utilisateur choisi
 */
class UserTouiteListAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $pageContent = parent::generationAction("SELECT touites.touiteID, touites.texte, utilisateurs.nom, utilisateurs.prenom, utilisateurs.utilisateurID, touites.datePublication, tags.tagID, tags.libelle
                    FROM touites
                    LEFT JOIN touitesutilisateurs ON touites.touiteID = touitesutilisateurs.TouiteID
                    LEFT JOIN utilisateurs ON touitesutilisateurs.utilisateurID = utilisateurs.utilisateurID
                    LEFT JOIN touitesimages ON touitesimages.TouiteID = touites.touiteID
                    LEFT JOIN images ON images.ImageID = touitesimages.ImageID
                    LEFT JOIN touitestags ON touites.touiteID = touitestags.TouiteID
                    LEFT JOIN tags ON touitestags.TagID = tags.TagID
                    WHERE utilisateurs.utilisateurID = :user_id
                    ORDER BY touites.datePublication DESC
                    LIMIT :limit OFFSET :offset",false,true);

        }
        return $pageContent;
    }
}
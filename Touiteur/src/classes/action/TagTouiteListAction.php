<?php

namespace iutnc\touiteur\action;

/**
 * Action permettant de voir la liste des touites d'un tag choisi
 */
class TagTouiteListAction extends Action
{
    // Constructeur
    public function __construct()
    {
        parent::__construct();
    }

    // Methode qui execute l'action
    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $pageContent = parent::generationAction("SELECT touites.touiteID, touites.texte, utilisateurs.utilisateurID, utilisateurs.prenom, utilisateurs.nom, touites.datePublication, tags.libelle, tags.tagID
                    FROM touites
                    LEFT JOIN touitesutilisateurs ON touites.touiteID = touitesutilisateurs.TouiteID
                    LEFT JOIN utilisateurs ON touitesutilisateurs.utilisateurID = utilisateurs.utilisateurID
                    LEFT JOIN touitesimages ON touitesimages.TouiteID = touites.touiteID
                    LEFT JOIN images ON images.ImageID = touitesimages.ImageID
                    LEFT JOIN touitestags ON touites.touiteID = touitestags.TouiteID
                    LEFT JOIN tags ON touitestags.TagID = tags.TagID
                    WHERE tags.TagID = :tag_id
                    ORDER BY touites.datePublication DESC
                    LIMIT :limit OFFSET :offset", true);

        }
        return $pageContent;
    }
}
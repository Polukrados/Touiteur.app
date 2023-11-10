<?php

namespace iutnc\touiteur\action;

class TagTouiteListAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $pageContent = parent::generationAction("SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.prenom, Utilisateurs.nom, Touites.datePublication, Tags.libelle, Tags.tagID
                    FROM Touites
                    LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                    LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                    LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.touiteID
                    LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                    LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                    LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                    WHERE Tags.TagID = :tag_id
                    ORDER BY Touites.datePublication DESC
                    LIMIT :limit OFFSET :offset", true);

        }
        return $pageContent;
    }
}
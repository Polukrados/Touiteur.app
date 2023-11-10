<?php

namespace iutnc\touiteur\action;

/**
 * Action par défaut
 */
class DefaultAction extends Action
{
    // Constructeur
    public function __construct()
    {
        parent::__construct();
    }

    // Méthode d'exécution
    public function execute(): string
    {
        $pageContent = "";
        // Si la méthode HTTP est GET, on affiche la page de connexion
        if ($this->http_method === 'GET') {
            // On génère le contenu de la page d'accueil donc on appelle la méthode generationAction qui va nous permettre de récupérer les données de la base de données
            $pageContent = parent::generationAction(
                "SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle
                            FROM Touites
                            LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                            LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                            LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.TouiteID
                            LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                            LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                            LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                            ORDER BY Touites.datePublication DESC
                            LIMIT :limit OFFSET :offset");

        }
        return $pageContent;
    }
}

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
                "SELECT touites.touiteID, touites.texte, utilisateurs.utilisateurID, utilisateurs.nom, utilisateurs.prenom, touites.datePublication, tags.tagID, tags.libelle
                            FROM touites
                            LEFT JOIN touitesutilisateurs ON touites.touiteID = touitesutilisateurs.TouiteID
                            LEFT JOIN utilisateurs ON touitesutilisateurs.utilisateurID = utilisateurs.utilisateurID
                            LEFT JOIN touitesimages ON touitesimages.TouiteID = touites.TouiteID
                            LEFT JOIN images ON images.ImageID = touitesimages.ImageID
                            LEFT JOIN touitestags ON touites.touiteID = touitestags.TouiteID
                            LEFT JOIN tags ON touitestags.TagID = tags.TagID
                            ORDER BY touites.datePublication DESC
                            LIMIT :limit OFFSET :offset");

        }
        return $pageContent;
    }
}

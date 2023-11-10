<?php

namespace iutnc\admin\action;

use iutnc\admin\db\ConnectionFactoryAdmin;
use PDO;

class InfluenceurAdmin extends ActionAdmin{

    public function execute(): string
    {
        // Connexion à la base de données
        $db = ConnectionFactoryAdmin::makeConnection();

        // Requête pour récupérer les utilisateurs les plus suivis
        $query = $db->prepare("SELECT utilisateurs.*, COUNT(suivi.suiviID) AS followers_count
                               FROM utilisateurs
                               LEFT JOIN suivi ON utilisateurs.utilisateurID = suivi.suiviID
                               GROUP BY utilisateurs.utilisateurID
                               ORDER BY followers_count DESC");

        $query->execute();

        // Construction de la liste des influenceurs
        $influencers = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $userName = $row['prenom'] . ' ' . $row['nom'];
            $followersCount = $row['followers_count'];

            $influencers .= <<<HTML
                <div class="influenceurs-container">
                    <div class="influenceur">
                        <p>$userName</p>
                        <p>Followers: $followersCount</p>
                    </div>
                </div>
            HTML;
        }

        // Construction de la page
        $pageContent = <<<HTML
            <header>
                <p class='libelle_page_courante'>Influenceurs</p>
                <nav class="menu-nav">
                </nav>
                <nav class="menu-nav">
                        <ul>
                            <a class="retour-arriere" href="?action=defaultadmin"><img src="images/retour_arriere.png" alt="Flèche retour arrière"></a>
                            <li><a href="?action=default"><i class="fa-solid fa-house"></i></a></li>
                        </ul>
                    </nav>
                    <nav class="menu">
                    <div class="photo-profil">
                        <a href="#lien_vers_profil_peut_etre_pas_oblige">
                            <img src="images/profile_icone.png" alt="Icône de profil">
                        </a>
                    </div>
                    <ul>
                        <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                        <li><a href="?action=default"><i class="fa-solid fa-house"></i></a></li>
                    </ul>
                    </nav>
            </header>
            <div class="influencers-list">
                $influencers
            </div>
        HTML;

        return $pageContent;
    }
}
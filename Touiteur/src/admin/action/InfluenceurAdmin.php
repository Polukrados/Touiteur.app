<?php

namespace iutnc\admin\action;

use iutnc\admin\db\ConnectionFactoryAdmin;
use PDO;

// Affiche la liste des influenceurs
class InfluenceurAdmin extends ActionAdmin
{

    // Fonction qui affiche la liste des influenceurs
    public function execute(): string
    {
        $db = ConnectionFactoryAdmin::makeConnection();
        // Récupère les influenceurs donc le nombre de followers est le plus élevé
        // Trier par ordre décroissant
        $query = $db->prepare("SELECT utilisateurs.*, COUNT(suivi.suiviID) AS followers_count
                               FROM utilisateurs
                               LEFT JOIN suivi ON utilisateurs.utilisateurID = suivi.suiviID
                               GROUP BY utilisateurs.utilisateurID
                               ORDER BY followers_count DESC");
        $query->execute();

        // Affiche les influenceurs
        $influencers = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // Affiche le nom et le nombre de followers des influenceurs
            $userName = $row['prenom'] . ' ' . $row['nom'];
            $followersCount = $row['followers_count'];

            // Rendu HTML de la liste des influenceurs
            $influencers .= <<<HTML
                <div class="influenceurs-container">
                    <div class="influenceur">
                        <p>$userName</p>
                        <p>Followers: $followersCount</p>
                    </div>
                </div>
            HTML;
        }

        return <<<HTML
            <header>
                <p class='libelle_page_courante'>Influenceurs</p>
                <nav class="menu-nav">
                </nav>
                <nav class="menu-nav">
                        <ul>
                            <a class="retour-arriere" href="?action=defaultadmin"><img src="images/retour_arriere.png" alt="Flèche retour arrière"></a>
                        </ul>
                    </nav>
            </header>
            <div class="influencers-list">
                $influencers
            </div>
        HTML;
    }
}
<?php

namespace iutnc\admin\action;

use iutnc\admin\db\ConnectionFactoryAdmin;
use PDO;

class InfluenceurAdmin extends ActionAdmin
{

    public function execute(): string
    {
        $db = ConnectionFactoryAdmin::makeConnection();
        $query = $db->prepare("SELECT Utilisateurs.*, COUNT(Suivi.suiviID) AS followers_count
                               FROM Utilisateurs
                               LEFT JOIN Suivi ON Utilisateurs.utilisateurID = Suivi.suiviID
                               GROUP BY Utilisateurs.utilisateurID
                               ORDER BY followers_count DESC");
        $query->execute();

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
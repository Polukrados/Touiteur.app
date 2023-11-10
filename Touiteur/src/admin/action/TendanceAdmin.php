<?php

namespace iutnc\admin\action;

use iutnc\admin\db\ConnectionFactoryAdmin;
use PDO;

class TendanceAdmin extends ActionAdmin{

    public function execute(): string
    {
        // Connexion à la base de données
        $db = ConnectionFactoryAdmin::makeConnection();

        // Requête pour récupérer les tendances
        $query = $db->query("SELECT tags.libelle, COUNT(touitestags.TagID) as count
                             FROM touitestags
                             JOIN tags ON touitestags.TagID = tags.tagID
                             GROUP BY touitestags.TagID
                             ORDER BY count ASC");

        // Récupération des tendances
        $trendingTags = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $tagLabel = $row['libelle'];
            $tagCount = $row['count'];

            $trendingTags .= <<<HTML
                <div class="trending-tag">
                    <span class="tag-label">$tagLabel</span>
                    <span class="tag-count">$tagCount mentions</span>
                </div>
            HTML;
        }

        // Construire la page avec les tendances
        $pageContent = <<<HTML
            <header>
                <!-- Your header content here -->
            </header>
            <div class="trending-tags-container">
                <h2>Tendances</h2>
                $trendingTags
            </div>
        HTML;

        return $pageContent;
    }
}
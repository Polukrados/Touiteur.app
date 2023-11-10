<?php

namespace iutnc\admin\action;

use iutnc\admin\db\ConnectionFactoryAdmin;
use PDO;

class TendanceAdmin extends ActionAdmin{

    public function execute(): string
    {
        $db = ConnectionFactoryAdmin::makeConnection();
        $query = $db->query("SELECT Tags.libelle, COUNT(TouitesTags.TagID) as count
                             FROM TouitesTags
                             JOIN Tags ON TouitesTags.TagID = Tags.tagID
                             GROUP BY TouitesTags.TagID
                             ORDER BY count desc");

        $trendingTags = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $tagLabel = $row['libelle'];
            $tagCount = $row['count'];

            $trendingTags .= <<<HTML
                <div class="influenceurs-container">
                    <div class="influenceur">
                        <div class="influenceur-name">$tagLabel</div>
                        <div class="influenceur-count">Mention : $tagCount</div>
                    </div>
                </div>
            HTML;
        }

        return <<<HTML
            <header>
                <div class="libelle_page_courante">Tendances</div>
                <nav class="menu-nav">
                        <ul>
                            <a class="retour-arriere" href="?action=defaultadmin"><img src="images/retour_arriere.png" alt="Flèche retour arrière"></a>
                        </ul>
                    </nav>
            </header>
                $trendingTags
        HTML;
    }
}
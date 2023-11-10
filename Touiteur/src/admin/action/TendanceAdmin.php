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
                             ORDER BY count desc");

        // Récupération des tendances
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

        // Construire la page avec les tendances
        $pageContent = <<<HTML
            <header>
                <div class="libelle_page_courante">Tendances</div>
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
                $trendingTags
        HTML;

        return $pageContent;
    }
}
<?php

namespace iutnc\admin\action;

use iutnc\admin\db\ConnectionFactoryAdmin;
use PDO;

/**
 * Classe gérant l'affichage des tendances.
 */
class TendanceAdmin extends ActionAdmin{

    // Méthode principale pour exécuter la logique de récupération des tendances.
    public function execute(): string
    {
        // Établissement de la connexion à la base de données via ConnectionFactoryAdmin.
        $db = ConnectionFactoryAdmin::makeConnection();

        // Requête SQL pour obtenir les libellés des tags et leur nombre d'occurrences.
        $query = $db->query("SELECT tags.libelle, COUNT(touitestags.TagID) as count
                             FROM touitestags
                             JOIN tags ON touitestags.TagID = tags.tagID
                             GROUP BY touitestags.TagID
                             ORDER BY count desc");

        // Initialisation de la variable qui va contenir les balises HTML représentant les tendances.
        $trendingtags = '';

        // Traitement de chaque ligne résultat de la requête.
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // Récupération du libellé et du comptage des tags de la base de données.
            $tagLabel = $row['libelle'];
            $tagCount = $row['count'];

            // Construction du HTML pour chaque tag tendance avec son nombre de mentions.
            $trendingtags .= <<<HTML
                <div class="influenceurs-container">
                    <div class="influenceur">
                        <div class="influenceur-name">$tagLabel</div>
                        <div class="influenceur-count">Mention : $tagCount</div>
                    </div>
                </div>
            HTML;
        }

        // Retourne le HTML complet de la page des tendances, y compris l'en-tête et les tags tendances.
        return <<<HTML
            <header>
                <div class="libelle_page_courante">Tendances</div>
                <nav class="menu-nav">
                        <ul>
                            <a class="retour-arriere" href="?action=defaultadmin"><img src="images/retour_arriere.png" alt="Flèche retour arrière"></a>
                        </ul>
                    </nav>
            </header>
                $trendingtags
        HTML;
    }
}
<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class DeleteTouiteAction extends Action
{
    private function ensureUserIsLoggedIn()
    {
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: ?action=signin');
            exit;
        }
    }

    public function execute(): string
    {
        $this->ensureUserIsLoggedIn();
        $delete_touite_html = "";

        if ($this->http_method == "POST") {
            $connection = ConnectionFactory::makeConnection();
            $touiteID = filter_input(INPUT_POST, 'touiteID', FILTER_SANITIZE_NUMBER_INT);

            // Vérifier si l'utilisateur est bien le propriétaire du touite
            if ($this->isUserOwnerOfTouite($touiteID, $_SESSION['utilisateur']['userID'])) {
                try {
                    // Suppression des tags associés au touite
                    $delete_tags_query = $connection->prepare("DELETE FROM touitestags WHERE touiteID = :touiteID");
                    $delete_tags_query->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $delete_tags_query->execute();

                    // Suppression des images associées au touite
                    $delete_images_query = $connection->prepare("DELETE FROM touitesimages WHERE touiteID = :touiteID");
                    $delete_images_query->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $delete_images_query->execute();

                    // Suppression du touite lui-même
                    $delete_touite_query = $connection->prepare("DELETE FROM touites WHERE touiteID = :touiteID");
                    $delete_touite_query->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $delete_touite_query->execute();

                    if ($delete_touite_query->rowCount() > 0) {
                        // Redirection vers la page d'accueil
                        header('Location: ?action=default');
                        exit;
                    } else {
                        throw new \Exception("Erreur lors de la suppression du touite.");
                    }
                } catch (\PDOException $e) {
                    $delete_touite_html .= "<p>Erreur de connexion à la base de données.</p>";
                } catch (\Exception $e) {
                    $delete_touite_html .= "<p>Erreur lors de la suppression du touite : " . $e->getMessage() . "</p>";
                }
            } else {
                $delete_touite_html .= "<p>Vous n'avez pas l'autorisation de supprimer ce touite.</p>";
            }
        }

        return $delete_touite_html;
    }
}
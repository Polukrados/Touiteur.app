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

        try {
            $connection = ConnectionFactory::makeConnection();
        } catch (\PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }

        if ($this->http_method == "GET") {
            $touiteID = $_GET['tweet_id'];
            $html = <<<HTML
                    <button style="background: none; border: none; top: 0; right: 0" onclick="window.location.href='index.php';" class="close-modal"><i class="fa fa-times"></i></button>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" class="libelle_page_courante">
                        <p> Voulez-vous vraiment supprimer ce touite ?</p>
                        <form class="form" method=post>
                            <input type=hidden name=touiteID value=$touiteID>
                            <input type=submit value=Supprimer>
                        </form>
                    </div>
            HTML;
            return $html;
        } else if ($this->http_method == "POST") {
            $touiteID = isset($_POST['touiteID']) ? intval($_POST['touiteID']) : null;

            // Vérifier si l'utilisateur est bien le propriétaire du touite
            if ($this->isUserOwnerOfTouite($touiteID, $_SESSION['utilisateur']['userID'])) {
                try {

                    $checktouitestags = $connection->prepare("SELECT count(*) AS count FROM touitestags WHERE touiteID = :touiteID");
                    $checktouitestags->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $checktouitestags->execute();
                    if ($checktouitestags->fetchColumn() != 0) {
                        // Suppression des tags associés au touite
                        $delete_touite = $connection->prepare("DELETE FROM touitestags WHERE touiteID = :touiteID");
                        $delete_touite->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                        $delete_touite->execute();
                    }

                    $checktouitesutilisateurs = $connection->prepare("SELECT count(*) AS count FROM touitesutilisateurs WHERE touiteID = :touiteID");
                    $checktouitesutilisateurs->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $checktouitesutilisateurs->execute();
                    if ($checktouitesutilisateurs->fetchColumn() != 0) {
                        // Suppression des tags associés au touite
                        $delete_touite = $connection->prepare("DELETE FROM touitesutilisateurs WHERE touiteID = :touiteID");
                        $delete_touite->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                        $delete_touite->execute();
                    }

                    $checktouitesimages = $connection->prepare("SELECT count(*) AS count FROM touitesimages WHERE touiteID = :touiteID");
                    $checktouitesimages->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $checktouitesimages->execute();
                    if ($checktouitesimages->fetchColumn() != 0) {
                        // Suppression des tags associés au touite
                        $delete_touite = $connection->prepare("DELETE FROM touitesimages WHERE touiteID = :touiteID");
                        $delete_touite->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                        $delete_touite->execute();
                    }

                    $checkevaluations = $connection->prepare("SELECT count(*) AS count FROM evaluations WHERE touiteID = :touiteID");
                    $checkevaluations->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $checkevaluations->execute();
                    if ($checkevaluations->fetchColumn() != 0) {
                        // Suppression des tags associés au touite
                        $delete_touite = $connection->prepare("DELETE FROM evaluations WHERE touiteID = :touiteID");
                        $delete_touite->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                        $delete_touite->execute();
                    }

                    // Suppression du touite lui-même
                    $delete_touite = $connection->prepare("DELETE FROM touites WHERE touiteID = :touiteID");
                    $delete_touite->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
                    $delete_touite->execute();

                    if ($delete_touite->rowCount() > 0) {
                        // Redirection vers la page d'accueil
                        header('Location: ?action=default');
                        exit;
                    } else {
                        throw new \Exception("Erreur lors de la suppression du touite.");
                    }
                } catch (\PDOException $e) {
                    $delete_touite_html .= "<p class='libelle_page_courante'>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>";
                } catch (\Exception $e) {
                    $delete_touite_html .= "<p class='libelle_page_courante'>Erreur lors de la suppression du touite : " . $e->getMessage() . "</p>";
                }
            } else {
                $delete_touite_html .= "<p class='libelle_page_courante'>Vous n'avez pas l'autorisation de supprimer ce touite.</p>";
            }
        }

        return $delete_touite_html;
    }

    private function isUserOwnerOfTouite($touiteID, $userID)
    {
        $connection = ConnectionFactory::makeConnection();

        try {
            $query = $connection->prepare("SELECT touiteID FROM touitesutilisateurs WHERE touiteID = :touiteID AND utilisateurID = :userID");
            $query->bindParam(':touiteID', $touiteID, PDO::PARAM_INT);
            $query->bindParam(':userID', $userID, PDO::PARAM_INT);
            $query->execute();

            return $query->rowCount() > 0;
        } catch (\PDOException $e) {
            echo 'Erreur lors de la vérification du propriétaire du touite : ' . $e->getMessage();
            return false;
        }
    }
}

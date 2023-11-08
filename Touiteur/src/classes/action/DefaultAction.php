<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\db\ConnectionFactory;
use PDO;

class DefaultAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }


    public function execute(): string
    {
        $pageContent = "";
        if ($this->http_method === 'GET') {
            $db = ConnectionFactory::makeConnection();
            $touitesParPages = 10;
            $pageCourante = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($pageCourante - 1) * $touitesParPages;

            $query = $db->prepare("SELECT Touites.touiteID, Touites.texte, Utilisateurs.utilisateurID, Utilisateurs.nom, Utilisateurs.prenom, Touites.datePublication, Tags.tagID, Tags.libelle
                                        FROM Touites
                                        LEFT JOIN TouitesUtilisateurs ON Touites.touiteID = TouitesUtilisateurs.TouiteID
                                        LEFT JOIN Utilisateurs ON TouitesUtilisateurs.utilisateurID = Utilisateurs.utilisateurID
                                        LEFT JOIN TouitesImages ON TouitesImages.TouiteID = Touites.TouiteID
                                        LEFT JOIN Images ON Images.ImageID = TouitesImages.ImageID
                                        LEFT JOIN TouitesTags ON Touites.touiteID = TouitesTags.TouiteID
                                        LEFT JOIN Tags ON TouitesTags.TagID = Tags.TagID
                                        ORDER BY Touites.datePublication DESC
                                        LIMIT :limit OFFSET :offset");

            $query->bindParam(':limit', $touitesParPages, PDO::PARAM_INT);
            $query->bindParam(':offset', $offset, PDO::PARAM_INT);
            $query->execute();

            $tweets = '';
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tweetID = $row['touiteID'];
                $userID = $row['utilisateurID'];
                $userName = $row['prenom'] . '_' . $row['nom'];
                $content = parent::texte($row['texte']);
                $tagID = $row['tagID'];
                $libelle = $row['libelle'];
                $timestamp = $row['datePublication'];

                // Touit court
                $tweets .= <<<HTML
            <div class="tweet">
                <div class="epingle-user">
                    <img src="images/epingle_user_rouge.png" alt="Image description" />  
                </div>
                <div class="user">
                    <a href='?action=user-touite-list&user_id=$userID'>
                        <i class="fa-solid fa-user" style="color: whitesmoke;"></i>
                    </a>
                    <a href='?action=user-touite-list&user_id=$userID'>$userName</a>
                    <a class="suivre" href='?action=follow-user&user_id=$userID'>
                        <i class="fa-solid fa-plus" id="follow-icon"></i>
                    </a>
                </div>
                <div class="content">
                    $content 
                    <a class="hashtag" href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a>
                </div>
                <div class="timestamp">Publié le : $timestamp</div>
                <a class="details-link" href="?action=details&tweet_id=$tweetID">Voir les détails</a>
            </div>
        HTML;
            }


            $countQuery = $db->query("SELECT COUNT(*) as total FROM Touites");

            $totalTouites = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalTouites / $touitesParPages);

            // Pagination
            $paginationLinks = '';
            for ($i = 1; $i <= $totalPages; $i++) {
                $paginationLinks .= "<a href='?action=default&page=$i'>$i</a> ";
            }

            // Page
            $pageContent = <<<HTML
            <header>
                <p class="libelle_page_courante">        
                    <a class="logo_touiteur" href="?action=default"><img src="images/logo_touiteur.png" alt="Logo de Touiteur"></a>       
                    Bienvenue sur Touiteur et pas Tracteur       
                </p>
                        <nav class="menu-nav">
                            <ul>
                                <li><a href="?action=add-user">S'inscrire</a></li>
                                <li><a href="?action=signin">Se connecter</a></li>
                                <li><a href="?action=default"><i class="fa-solid fa-house"></i></a></li>
                            </ul>
                        </nav>
                <nav class="menu">
                    <div class="photo-profil">
                        <a href="#lien_vers_profil_peut_etre_pas_oblige">
                            <img src="images/gaetan.png" alt="Icône de profil">
                        </a>
                    </div>
                    <ul>
                        <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                        <li><a href="?action=add-user">S'inscrire</a></li>
                        <li><a href="?action=signin">Se connecter</a></li>
                    </ul>
                </nav>
            </header>
            <div class="tweets">
                $tweets
            </div>
            <div class="pagination">
                $paginationLinks
            </div>
HTML;
        }
        return $pageContent;
    }
}

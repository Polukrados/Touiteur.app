<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

abstract class Action
{
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct()
    {

        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    protected function texte($text, $maxLength = 200, $suffix = '...'): string
    {
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . $suffix;
        }
        return $text;
    }

    protected function generationAction($query, $tag = false, $listUser = false, $user = false): string
    {
        $db = ConnectionFactory::makeConnection();
        $touitesParPages = 10;
        $pageCourante = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($pageCourante - 1) * $touitesParPages;

        $query = $db->prepare($query);
        if ($tag === true) {
            $tagID = intval($_GET['tag_id']);
            $query->bindParam(':tag_id', $tagID, PDO::PARAM_INT);
        }
        if ($listUser === true || $user === true) {
            $userID = intval($_GET['user_id']);
            $query->bindParam(':user_id', $userID, PDO::PARAM_INT);
        }
        $query->bindParam(':limit', $touitesParPages, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->execute();


        $tweets = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $tweetID = $row['touiteID'];
            $userID = $row['utilisateurID'];
            $userName = $row['prenom'] . '_' . $row['nom'];
            $content = $this->texte($row['texte']);
            $tagID = $row['tagID'];
            $libelle = $row['libelle'];
            $timestamp = $row['datePublication'];

            // Touit court
            if ($tag === true || $listUser === true || $user === true) {
                $tweets .= <<<HTML
            <div class="template-feed">
                <div class="user">
                     <a href='?action=user-touite-list&user_id=$userID'><i class="fa-solid fa-user" style="color: whitesmoke;"></i></a>
                     <a href='?action=user-touite-list&user_id=$userID'>$userName</a>
                </div>  
                <div class="content">
                    $content 
                    <a class="hashtag" href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a>
                </div>
                <div class="timestamp">Publié le : $timestamp</div>
                <a class="details-link" href="?action=details&tweet_id=$tweetID">Voir les détails</a>
            </div>
        HTML;
            } else {
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
                                    <a class="suivre" href='?action=profile-user&user_id=$userID'>
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
        }

        $countQuery = $db->query("SELECT COUNT(*) as total FROM Touites");

        $totalTouites = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalTouites / $touitesParPages);

        // Pagination
        $paginationLinks = '';
        for ($i = 1; $i <= $totalPages; $i++) {
            $paginationLinks .= "<a href='?action=default&page=$i'>$i</a> ";
        }

        if ($tag === true) {
            $res = "<header>
            <p class='libelle_page_courante'>$libelle</p>";
        } else if ($listUser === true) {
            $res = "<header>
            <p class='libelle_page_courante'>Touites de $userName</p>";
        } else if ($user === true) {
            $res = <<<HTML
                    <div class="tweets">
            $tweets
            </div>
            <div class="pagination">
            $paginationLinks
            </div>;
            HTML;

        } else {
            $res = "<header>
            <p class='libelle_page_courante'>        
                    <a class='logo_touiteur' href='?action=default'><img src='images/logo_touiteur.png' alt='Logo de Touiteur'></a>       
                    Bienvenue sur Touiteur et pas Tracteur       
                </p>";
        }
        if ($user===true){
            $pageContent=$res;
        }else {
            $pageContent = <<<HTML
                        $res
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

    abstract public function execute(): string;

}

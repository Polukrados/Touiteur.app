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

    protected function generationAction($query,$tag=false,$listUser=false) :string
    {
        $db = ConnectionFactory::makeConnection();
        $touitesParPages = 10;
        $pageCourante = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($pageCourante - 1) * $touitesParPages;

        $query = $db->prepare($query);
        if($tag===true){
            $tagID = intval($_GET['tag_id']);
            $query->bindParam(':tag_id', $tagID, PDO::PARAM_INT);
        }
        if($listUser===true){
            $userID = intval($_GET['user_id']);
            $query->bindParam(':user_id', $userID, PDO::PARAM_INT);
        }
        $query->bindParam(':limit', $touitesParPages, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->execute();


        $tweets = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $tweetID = $row['touiteID'];
            if($listUser===false){
                $userID = $row['utilisateurID'];
            }
            $userName = $row['prenom'] . '_' . $row['nom'];
            $content = $this->texte($row['texte']);
            $tagID = $row['tagID'];
            $libelle = $row['libelle'];
            $timestamp = $row['datePublication'];

                // Touit court
            if($tag===true || $listUser===true){
                $tweets.=<<<HTML
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
            }else {
                $tweets .= <<<HTML
            <div class="tweet">
                <div class="user">Utilisateur: <a href='?action=user-touite-list&user_id=$userID'>$userName</a> - <a href='?action=profile-user&user_id=$userID'>Profil</a></div>
                <div class="content">$content <a href='?action=tag-touite-list&tag_id=$tagID'>$libelle</a></div>
                <div class="timestamp">Publié le : $timestamp</div>
                <a href="?action=details&tweet_id=$tweetID">Voir les détails</a>
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

        if ($tag===true){
            $res="<header>
            <p class='libelle_page_courante'>$libelle</p>";
        }else if($listUser===true){
            $res="<header>
            <p class='libelle_page_courante'>Touites de $userName</p>";
        }else{
            $res="<header>
            <p class='libelle_page_courante'>Accueil</p>";
        }
        $pageContent = <<<HTML
            $res
            <nav class="menu">
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
        return $pageContent;

    }

    abstract public function execute(): string;

}

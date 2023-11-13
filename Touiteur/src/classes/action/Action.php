<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\db\ConnectionFactory;
use PDO;

// Classe abstraite Action qui sert de base pour toutes les actions dans l'application.
abstract class Action
{
    // Propriétés pour stocker les informations de la requête HTTP.
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    // Le constructeur initialise les propriétés avec les valeurs globales $_SERVER.
    public function __construct()
    {
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    // Méthode pour tronquer un texte à une longueur donnée avec un suffixe.
    protected function texte($text, $maxLength = 200, $suffix = '...'): string
    {
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . $suffix;
        }
        return $text;
    }

    // Méthode générale pour la génération du contenu des actions.
    protected function generationAction($query, $tag = false, $listUser = false, $user = false, $display = false): string
    {
        //Pagination
        $db = ConnectionFactory::makeConnection();

        // Définition du nombre de touites par page pour la pagination.
        $touitesParPages = 10;
        // Détermination de la page courante basée sur le paramètre 'page' de l'URL.
        $pageCourante = isset($_GET['page']) ? intval($_GET['page']) : 1;
        // Initialisation des variables pour le nom d'utilisateur et le libellé du tag.
        $offset = ($pageCourante - 1) * $touitesParPages;

        $userName = '';
        $libelle = '';
        // Calcul de l'offset pour la requête SQL basée sur la pagination.
        $offset = ($pageCourante - 1) * $touitesParPages;

        // Préparation de la requête SQL avec des paramètres nommés pour la sécurité.
        $query = $db->prepare($query);

        // Liaison des paramètres SQL si la requête dépend des tags, des utilisateurs ou d'affichage spécifique.
        if ($tag === true) {
            $tagID = intval($_GET['tag_id']);
            $query->bindParam(':tag_id', $tagID, PDO::PARAM_INT);
        }
        if ($listUser === true || $user === true || $display === true) {
            $userID = $_GET['user_id'];
            if ($display === true) {
                $query->bindParam(':user_id1', $userID, PDO::PARAM_INT);
                $query->bindParam(':user_id2', $userID, PDO::PARAM_INT);
            } else {
                $query->bindParam(':user_id', $userID, PDO::PARAM_INT);
            }
        }

        // Liaison des paramètres pour la limite et l'offset dans la requête paginée.
        $query->bindParam(':limit', $touitesParPages, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Exécution de la requête.
        $query->execute();

        // Initialisation de la variable qui contiendra les touites sous forme de HTML.
        $tweets = '';
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            // Récupération des données pour chaque touite
            $tweetID = $row['touiteID'];
            $userID = $row['utilisateurID'];
            $content = $this->texte($row['texte']);
            $userName = $row['prenom'] . '_' . $row['nom'];
            $tagID = $row['tagID'];
            $libelle = $row['libelle'];
            $timestamp = $row['datePublication'];

            // Touit court
            // Si l'utilisateur est connecté, on affiche les touites avec les actions possibles.
            if (isset($_SESSION['utilisateur'])) {
                if ($tag === true || $listUser === true || $user === true) {
                    $tweets .= <<<HTML
                                            <div class="template-all">
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
                                                <div class="tweet-actions">
                                                    <a href="?action=like&tweet_id=$tweetID" class="tweet-action like">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                    </a>
                                                    <a href="?action=dislike&tweet_id=$tweetID" class="tweet-action dislike">
                                                        <i class="fa-solid fa-thumbs-down"></i>
                                                    </a>
                                                    <a href="?action=retweet&tweet_id=$tweetID" class="tweet-action retweet">
                                                        <i class="fa-solid fa-retweet"></i>
                                                    </a>
                                                    <a href="?action=reply&tweet_id=$tweetID" class="tweet-action reply">
                                                        <i class="fa-solid fa-reply"></i>
                                                    </a>
                                            HTML;
                    // Si l'utilisateur est l'auteur du touite, on affiche l'action de suppression.
                    if ($_SESSION['utilisateur']['userID'] == $userID) {
                        $tweets .= <<<HTML
                                                            <a href="?action=delete&tweet_id=$tweetID" class="tweet-action trash">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                HTML;
                    }
                    $tweets .= <<<HTML
                                                </div>
                                            </div>
                                            HTML;
                    // Sinon, on affiche les touites sans les actions.
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
                                                                              <div class="tweet-actions">
                                                                                  <a href="?action=like&tweet_id=$tweetID" class="tweet-action like">
                                                                                      <i class="fa-solid fa-thumbs-up"></i>
                                                                                  </a>
                                                                                  <a href="?action=dislike&tweet_id=$tweetID" class="tweet-action dislike">
                                                                                      <i class="fa-solid fa-thumbs-down"></i>
                                                                                  </a>
                                                                                  <a href="?action=retweet&tweet_id=$tweetID" class="tweet-action retweet">
                                                                                      <i class="fa-solid fa-retweet"></i>
                                                                                  </a>
                                                                                  <a href="?action=reply&tweet_id=$tweetID" class="tweet-action reply">
                                                                                      <i class="fa-solid fa-reply"></i>
                                                                                  </a>
                                 HTML;
                    // Si l'utilisateur est l'auteur du touite, on affiche l'action de suppression.
                    if ($_SESSION['utilisateur']['userID'] == $userID) {
                        $tweets .= <<<HTML
                                                                                         <a href="?action=delete&tweet_id=$tweetID" class="tweet-action trash">
                                                                                            <i class="fa-solid fa-trash"></i>
                                                                                         </a>
                                                                                HTML;
                    }
                    $tweets .= <<<HTML
                                                                              </div>
                                                                          </div>
                                                                          HTML;
                }
                // Sinon, on affiche les touites sans les actions.
            } else {
                if ($tag === true || $listUser === true || $user === true) {
                    $tweets .= <<<HTML
                                            <div class="template-all">
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
                                                <div class="tweet-actions">
                                                    <a href="?action=like&tweet_id=$tweetID" class="tweet-action like">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                    </a>
                                                    <a href="?action=dislike&tweet_id=$tweetID" class="tweet-action dislike">
                                                        <i class="fa-solid fa-thumbs-down"></i>
                                                    </a>
                                                    <a href="?action=retweet&tweet_id=$tweetID" class="tweet-action retweet">
                                                        <i class="fa-solid fa-retweet"></i>
                                                    </a>
                                                    <a href="?action=reply&tweet_id=$tweetID" class="tweet-action reply">
                                                        <i class="fa-solid fa-reply"></i>
                                                    </a>
                                                </div>
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
                                                                              <div class="tweet-actions">
                                                                                  <a href="?action=like&tweet_id=$tweetID" class="tweet-action like">
                                                                                      <i class="fa-solid fa-thumbs-up"></i>
                                                                                  </a>
                                                                                  <a href="?action=dislike&tweet_id=$tweetID" class="tweet-action dislike">
                                                                                      <i class="fa-solid fa-thumbs-down"></i>
                                                                                  </a>
                                                                                  <a href="?action=retweet&tweet_id=$tweetID" class="tweet-action retweet">
                                                                                      <i class="fa-solid fa-retweet"></i>
                                                                                  </a>
                                                                                  <a href="?action=reply&tweet_id=$tweetID" class="tweet-action reply">
                                                                                      <i class="fa-solid fa-reply"></i>
                                                                                  </a>
                                                                              </div>
                                                                          </div>
                HTML;
                }
            }
        }
        // Nombre total de touites
        $countQuery = $db->query("SELECT COUNT(*) as total FROM touites");

        // Nombre de pages
        $totaltouites = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totaltouites / $touitesParPages);

        // Pagination
        $paginationLinks = '';
        for ($i = 1; $i <= $totalPages; $i++) {
            $paginationLinks .= "<a href='?action=default&page=$i'>$i</a> ";
        }

        $pasabo = "<div class='tweets'>
            $tweets
                        </div>
                        <div class='pagination'>
            $paginationLinks
                        </div>";

        $logo = "<a class='logo_touiteur' href='?action=default'><img src='images/logo_touiteur.png' alt='Logo de Touiteur'></a>";
        /******************************************************
         *                                                    *
         *        Quand l'utilisateur est connecté            *
         *                                                    *
         ******************************************************/
        // Si l'utilisateur est connecté, on affiche le menu de navigation avec les actions possibles.
        if (isset($_SESSION['utilisateur'])) {
            if ($tag === true) {
                $res = "<header>$logo<p class='libelle_page_courante'>$libelle</p>";
            } else if ($listUser === true) {
                $res = "<header>$logo<p class='libelle_page_courante'>touites de $userName</p>";
            } else if ($user === true) {
                $res = "<div class='tweets'>$tweets</div><div class='pagination'>$paginationLinks</div>";
                $pasabo = "";
            } else {
                $res = "<header>
                                $logo
                            <div class='container-titre'>
                                <p id='touiteur' class='libelle_page_courante'></p>
                            </div>

                        <nav class='menu-nav'>
                            <ul>
                                <li><a href='?action=display-abo&user_id={$_SESSION['utilisateur']['userID']}'>Abonnement</a></li>
                                <li><a href='?action=default'>Pour toi</a></li>
                            </ul>
                        </nav>";
            }
            // Affichage des tags que l'utilisateur suit.
            if ($display === true) {
                $res .= "
                        <div class='form-container'>
                            <h2 style='text-align: center'>tags</h2>
                            <form class='form' action='?action=display-abo&user_id={$_SESSION['utilisateur']['userID']}' method='post'>
                                <input type='text' placeholder='S abonner à un tag' name='f' id='tagsearch' class='input-icon-email'>
                                <input type='submit' value='S abonner'>
                            </form>
                            <br>
                            <form class='form' action='?action=display-abo&user_id={$_SESSION['utilisateur']['userID']}' method='post'>
                                <input type='text' placeholder='Se désabonner d un tag' name='u' id='tagsearch' class='input-icon-email'>
                                <input type='submit' value='Se désabonner'>
                            </form>
                        </div>
                        <div class='separator'></div>
                        <h2 style='text-align: center'>Liste des touites des personnes ou des tags que vous suivez :</h2>
                    ";
            }
            return <<<HTML
                        $res
                        <nav class="menu">
                            <div class="photo-profil">
                                <a href="#lien_vers_profil_peut_etre_pas_oblige">
                                    <img src="images/profile_icone.png" alt="Icône de profil">
                                </a>
                            </div>
                            <ul>
                                <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                                <li><a href="?action=profile-user&user_id={$_SESSION['utilisateur']['userID']}">Mon profil</a></li>
                                <li><a href="?action=logout">Se déconnecter</a></li>
                            </ul>
                        </nav>
                        </header>
                        $pasabo
            HTML;
        } /******************************************************
         *                                                    *
         *        Quand l'utilisateur n'est pas connecté      *
         *                                                    *
         ******************************************************/

        else {
            if ($tag === true) {
                $res = "<header>$logo<p class='libelle_page_courante'>$libelle</p>";
            } else if ($listUser === true) {
                $res = "<header>$logo<p class='libelle_page_courante'>touites de $userName</p>";
            } else if ($user === true) {
                $res = "<div class='tweets'>$tweets</div><div class='pagination'>$paginationLinks</div>";
                $pasabo = "";
            } else {
                $res = "<header>
                            <div class='container-titre'>
                                <a class='logo_touiteur' href='?action=default'>
                                    <img src='images/logo_touiteur.png' alt='Logo de Touiteur'>
                                </a>
                                <p id='touiteur' class='libelle_page_courante'></p>
                            </div>";
            }
            if ($user === true) {
                return $res;
            } else {
                return <<<HTML
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
                            <img src="images/profile_icone.png" alt="Icône de profil">
                        </a>
                    </div>
                    <ul>
                        <li><a href="?action=post-touite" class="publish-btn">Publier un touite</a></li>
                        <li><a href="?action=add-user">S'inscrire</a></li>
                        <li><a href="?action=signin">Se connecter</a></li>
                    </ul>
                    </nav>
                    </header>
                    $pasabo
                    HTML;
            }
        }
    }

    abstract public function execute(): string;
}

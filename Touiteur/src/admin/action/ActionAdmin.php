<?php

namespace iutnc\admin\action;

/**
 * Classe abstraite gérant les actions de l'admin.
 */
abstract class ActionAdmin
{
    // Attributs
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    // Méthodes
    // Constructeur
    public function __construct()
    {
        // Initialisation des attributs
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    // Méthode de redirection
    protected function texte($text, $maxLength = 200, $suffix = '...'): string
    {
        // Suppression des balises HTML
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . $suffix;
        }
        return $text;
    }


    // Méthode abstraite d'exécution
    abstract public function execute(): string;
}

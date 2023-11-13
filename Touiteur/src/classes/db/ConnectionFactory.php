<?php

namespace iutnc\touiteur\db;

use PDO;

/**
 * Action permettant de se connecter à la base de donnees
 */
class ConnectionFactory
{

    private static $tableau = [];

    public static function setConfig($file)
    {
        self::$tableau = parse_ini_file($file);
    }

    // methode de connexion
    public static function makeConnection(): PDO
    {
        $dns = self::$tableau['driver'] . ":host=" . self::$tableau['host'] . ";dbname=" . self::$tableau['database'];
        $username = self::$tableau['username'];
        $password = self::$tableau['password'];

        $db = new PDO($dns, $username, $password, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);

        $db->prepare('SET NAMES \'UTF8\'')->execute();

        return $db;
    }
}

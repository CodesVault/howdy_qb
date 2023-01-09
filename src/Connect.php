<?php

namespace CodesVault\Howdyqb;

final class Connect
{
    private static $configs;

    public static function config($configs)
    {
        static::$configs = $configs;
    }

    public static function pdo()
    {
        global $wpdb;

        $configs = static::$configs ? (object)static::$configs : $wpdb;
        $host = htmlspecialchars($configs->dbhost, ENT_QUOTES);
        $dbname = htmlspecialchars($configs->dbname, ENT_QUOTES);
        $user = htmlspecialchars($configs->dbuser, ENT_QUOTES);
        $password = htmlspecialchars($configs->dbpassword, ENT_QUOTES);
        $dns =  "mysql:host=$host;dbname=$dbname";

        try {
            return new \PDO($dns, $user, $password);
        } catch (\PDOException $exception) {
            Utilities::throughException($exception);
        }
    }
}

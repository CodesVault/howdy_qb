<?php

namespace CodesVault\Howdyqb;

final class Connect
{
    public static function pdo()
    {
        global $wpdb;

        $host = htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES);
        $dbname = htmlspecialchars($wpdb->dbname, ENT_QUOTES);
        $user = htmlspecialchars($wpdb->dbuser, ENT_QUOTES);
        $password = htmlspecialchars($wpdb->dbpassword, ENT_QUOTES);
        $dns =  "mysql:host=$host;dbname=$dbname";

        try {
            return new \PDO($dns, $user, $password);
        } catch (\PDOException $exception) {
            $error_msg = sprintf(
                "<strong style='color: red;'>%s</strong>",
                $exception->getMessage()
            );
            throw new \Exception( $error_msg );
        }
    }
}

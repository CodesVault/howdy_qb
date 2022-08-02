<?php

namespace CodesVault\WPqb;

final class Connect
{
    public static function pdo()
    {
        global $wpdb;
        $host = $_SERVER['HTTP_HOST'];
        $dns =  'mysql:host=' . $host . ';dbname=' . $wpdb->dbname;
        $user = $wpdb->dbuser;
        $password = $wpdb->dbpassword;

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

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
        return new \PDO($dns, $user, $password);
    }
}

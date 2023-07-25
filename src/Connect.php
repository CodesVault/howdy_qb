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

	public static function setManualConnection(array $configs = [])
	{
		$configurations = [];
		if (! defined('DB_HOST')) {
			$path = explode('/wp-content', dirname(__DIR__));
			if (! empty($path) && ! file_exists($path[0] . '/wp-config.php')) {
				throw new \Exception('wp-config.php file not found');
			}
			require $path[0] . '/wp-config.php';

			$configurations = [
				"dbhost"        => DB_HOST,
				"dbname"        => DB_NAME,
				"dbuser"        => DB_USER,
				"dbpassword"    => DB_PASSWORD,
				"prefix"        => $table_prefix,
			];
		}

		$configurations = array_merge($configurations, $configs);
        static::$configs = (object)$configurations;
	}
}

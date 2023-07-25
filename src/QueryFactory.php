<?php

namespace CodesVault\Howdyqb;

use CodesVault\Howdyqb\Api\SelectInterface;
use CodesVault\Howdyqb\Statement\Alter;
use CodesVault\Howdyqb\Statement\Create;
use CodesVault\Howdyqb\Statement\Delete;
use CodesVault\Howdyqb\Statement\Drop;
use CodesVault\Howdyqb\Statement\Insert;
use CodesVault\Howdyqb\Statement\Select;
use CodesVault\Howdyqb\Statement\Update;

class QueryFactory
{
    protected static $db = null;
    protected static $driver = 'pdo';
    private static $config;

    public static function getDriver()
    {
        return static::$driver;
    }

    public function __construct($driver = 'pdo')
    {
        if (self::$db) return;

        if ('pdo' === $driver) {
            self::$db = Connect::pdo();
        } elseif ('wpdb' === $driver) {
            global $wpdb;
            self::$db = $wpdb;
        }
    }

	/**
	 * Set manula connection
	 *
	 * @param array $configs
	 * @param string $driver
	 * @return CodesVault\Howdyqb\DB
	 */
	public static function setConnection($configs = [], $driver = 'pdo')
    {
		if (! empty($configs)) {
			Connect::setManualConnection($configs);
		}
		return new DB($driver);
    }

    protected static function selectQuery(): SelectInterface
    {
        return new Select(self::$db);
    }

    protected function insertQuery(string $table_name, array $data)
    {
        return new Insert($this->db, $table_name, $data);
    }

    protected function createQuery(string $table_name)
    {
        return new Create($this->db, $table_name);
    }

	protected function alterQuery(string $table_name)
    {
        return new Alter($this->db, $table_name);
    }

    protected function updateQuery(string $table_name, array $data)
    {
        return new Update($this->db, $table_name, $data);
    }

    protected function deleteQuery(string $table_name)
    {
        return new Delete($this->db, $table_name);
    }

    protected function dropQuery(string $table_name)
    {
        return new Drop($this->db, $table_name);
    }

    public static function getConfig()
    {
        return static::$config;
    }
}

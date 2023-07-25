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
    protected $db = null;
    protected static $driver = 'pdo';
    private static $config;

    public static function getDriver()
    {
        return static::$driver;
    }

    public function __construct($driver = 'pdo')
    {
        if ($this->db) return;

		static::$driver = $driver;

        if ('pdo' === $driver) {
            $this->db = Connect::pdo();
        } elseif ('wpdb' === $driver) {
            global $wpdb;
            $this->db = $wpdb;
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
		static::$config = $configs;
		return new DB($driver);
    }

    protected function selectQuery(): SelectInterface
    {
        return new Select($this->db);
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

<?php

namespace CodesVault\WPqb;

use CodesVault\WPqb\Api\SelectInterface;
use CodesVault\WPqb\Statement\Create;
use CodesVault\WPqb\Statement\Insert;
use CodesVault\WPqb\Statement\Select;

class QueryFactory
{
    protected $db = null;
    protected static $driver = 'pdo';

    public static function setDriver(string $driver)
    {
        static::$driver = $driver;
    }

    public function __construct($driver = 'pdo')
    {
        if ( ! $this->db && $driver === 'pdo' ) {
            $this->db = Connect::pdo();
        }
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
}

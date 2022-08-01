<?php

namespace CodesVault\WPqb;

use CodesVault\WPqb\Api\InsertInterface;
use CodesVault\WPqb\Api\SelectInterface;
use CodesVault\WPqb\Expression\Insert;
use CodesVault\WPqb\Expression\Select;

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

    protected function insertQuery(string $table_name, array $data): InsertInterface
    {
        return new Insert($this->db, $table_name, $data);
    }
}

<?php

namespace CodesVault\WPqb;

use CodesVault\WPqb\Api\SelectInterface;
use CodesVault\WPqb\Expression\Select;

class QueryFactory
{
    protected $db;
    protected static $driver = 'pdo';

    public static function setDriver(string $driver)
    {
        static::$driver = $driver;
    }

    public function __construct($driver = 'pdo')
    {
        if ( $driver === 'pdo' ) {
            $this->db = Connect::pdo();
        }
    }

    public function selectQuery(): SelectInterface
    {
        return new Select($this->db);
    }
}

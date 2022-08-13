<?php

namespace CodesVault\Howdyqb;

use CodesVault\Howdyqb\Api\SelectInterface;
use CodesVault\Howdyqb\Statement\Create;
use CodesVault\Howdyqb\Statement\Delete;
use CodesVault\Howdyqb\Statement\Insert;
use CodesVault\Howdyqb\Statement\Select;
use CodesVault\Howdyqb\Statement\Update;

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

    protected function updateQuery(string $table_name, array $data)
    {
        return new Update($this->db, $table_name, $data);
    }

    protected function deleteQuery(string $table_name)
    {
        return new Delete($this->db, $table_name);
    }
}

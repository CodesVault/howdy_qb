<?php

namespace CodesVault\Howdyqb\Tests\Statement;

use CodesVault\Howdyqb\Statement\Create;
use CodesVault\Howdyqb\SqlGenerator;
use PHPUnit\Framework\TestCase;

class CreateApi extends Create
{
    public function __construct($db, string $table_name)
    {
        $this->db = $db;
        $this->table_name = $table_name;
        $this->wpdb_object = new \stdClass;
        $this->wpdb_object->prefix = 'wp_';

        $this->start();
        $this->sql['table_name'] = $this->get_table_name();
    }

    public static function create($table_name)
    {
        $driver = new \stdClass;
        $create = new self($driver, $table_name);
        return $create;
    }

    public function getSql()
    {
        $this->start();
        $query = SqlGenerator::create($this->sql);

        return $query;
    }
}

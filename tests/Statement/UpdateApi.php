<?php

namespace CodesVault\Howdyqb\Tests\Statement;

use CodesVault\Howdyqb\Statement\Update;
use CodesVault\Howdyqb\SqlGenerator;

class UpdateApi extends Update
{
    public function __construct($db, string $table_name, array $data)
    {
        $this->db = $db;
        $this->data = $data;
        $this->wpdb_object = new \stdClass;

        $this->table_name = 'wp_' . $table_name;
        $this->sql['set_columns'] = $this->set_columns();
    }

    public static function update(string $table_name, array $data)
    {
        $driver = new \stdClass;
        $update = new self($driver, $table_name, $data);
        return $update;
    }

    public function getSql()
    {
        $this->start();
        $query = SqlGenerator::update($this->sql);

        return $query;
    }

    public function getParams()
    {
        return $this->params;
    }
}

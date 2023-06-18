<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\DropInterface;
use CodesVault\Howdyqb\QueryFactory;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;

class Drop implements DropInterface
{
    protected $db;
    public $sql = [];
    protected $params = [];
    protected $table_name;
    protected $wpdb_object;

    public function __construct($db, string $table_name)
    {
        $this->wpdb_object = QueryFactory::getConfig();
        if (empty(QueryFactory::getConfig())) {
            global $wpdb;
            $this->wpdb_object = $wpdb;
        }

        $this->db = $db;
        $this->table_name = $this->wpdb_object->prefix . $table_name;
    }

    public function drop()
    {
        $this->sql['drop'] = 'DROP TABLE ' . $this->table_name;
        $this->drop_table();
    }

    public function dropIfExists()
    {
        $this->sql['drop'] = 'DROP TABLE IF EXISTS ' . $this->table_name;
        $this->drop_table();
    }

    private function driver_exicute($sql)
    {
        $driver = $this->db;
        if ('wpdb' === QueryFactory::getDriver()) {
            return $driver->query($sql);
        }

        $data = $driver->prepare($sql);
        return $data->execute($this->params);
    }

    private function drop_table()
    {
        $query = trim($this->sql['drop']);

        try {
            $this->driver_exicute($query);
        } catch (\Exception $exception) {
            Utilities::throughException($exception);
        }
    }
}

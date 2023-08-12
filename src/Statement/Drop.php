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
        $this->db = $db;
        $this->table_name = Utilities::get_db_configs()->prefix . $table_name;
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

    private function driver_execute($sql)
    {
        $driver = $this->db;
		if (class_exists('wpdb') && $driver instanceof \wpdb) {
            return $driver->query($sql);
        }

        $data = $driver->prepare($sql);
		try {
			return $data->execute($this->params);
        } catch (\Exception $exception) {
            Utilities::throughException($exception);
        }
    }

    private function drop_table()
    {
        $query = trim($this->sql['drop']);

        $this->driver_execute($query);
    }
}

<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\DropInterface;
use CodesVault\Howdyqb\SqlGenerator;

class Drop implements DropInterface
{
    protected $db;
    public $sql = [];
    protected $params = [];
    protected $table_name;
    protected $wpdb_object;

    public function __construct($db, string $table_name)
    {
        global $wpdb;
        $this->wpdb_object = $wpdb;

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

    private function drop_table()
    {
        $query = trim($this->sql['drop']);

        $conn = $this->db;
        try {
            $data = $conn->prepare($query);
            return $data->execute($this->params);
        } catch (\Exception $exception) {
            $error_msg = sprintf(
                "<strong style='color: #d60202;'>%s</strong>  <strong style='color: red;'>%s</strong><br/>",
                'ERROR Message',
                $exception->getMessage()
            );
            printf($error_msg);
            throw new \Exception($error_msg);
        }
    }
}

<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\InsertInterface;
use CodesVault\Howdyqb\SqlGenerator;

class Insert
{
    protected $db;
    protected $data = [];
    public $sql = [];
    public $test = [];
    protected $params = [];
    private $table_name;

    public function __construct($db, string $table_name, array $data)
    {
        $this->db = $db;
        $this->data = $data;
        $this->table_name = $table_name;

        $this->start();
        $this->sql['table_name'] = $this->get_table_name();
        $this->sql['columns'] = $this->get_columns();
        $this->sql['value_placeholders'] = $this->get_value_placeholders();
        $this->params = $this->get_params();
        
        $this->insert_data();
    }

    private function insert_data()
    {
        $query = SqlGenerator::insert($this->sql);

        $conn = $this->db;
        try {
            $data = $conn->prepare($query);
            return $data->execute($this->params);
        } catch (\Exception $exception) {
            $error_msg = sprintf(
                "<strong style='color: red;'>%s</strong>",
                $exception->getMessage()
            );
            throw new \Exception($error_msg);
        }
    }

    private function start()
    {
        $this->sql['start'] = 'INSERT INTO';
    }

    private function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . $this->table_name;
    }

    private function get_columns()
    {
        if (empty($this->data)) return;

        $columns = [];
        foreach ($this->data[0] as $column => $value) {
            $columns[] = $column;
        }
        return '(' . implode(', ', $columns) . ')';
    }

    private function get_value_placeholders()
    {
        $placeholders = [];

        if (count($this->data) > 1) {
            foreach ($this->data as $row) {
                $placeholders[] = '(' . implode(',', array_fill(0, count($row), '?')) . ')';
            }
        } else {
            $placeholders[] = '(' . implode(',', array_fill(0, count($this->data[0]), '?')) . ')';
        }
        return 'VALUES ' . implode(',', $placeholders);
    }

    private function get_params()
    {
        $params = [];
        foreach ($this->data as $value) {
            foreach ($value as $val) {
                $params[] = $val;
            }
        }
        return $params;
    }
}

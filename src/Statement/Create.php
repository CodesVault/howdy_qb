<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\CreateInterface;
use CodesVault\Howdyqb\SqlGenerator;

class Create implements CreateInterface
{
    protected $db;
    public $sql = [];
    protected $params = [];
    protected $column_name;
    protected $wpdb_object;

    public function __construct($db, string $table_name)
    {
        global $wpdb;
        $this->db = $db;
        $this->table_name = $table_name;
        $this->wpdb_object = $wpdb;

        $this->start();
        $this->sql['table_name'] = $this->get_table_name();
    }

    public function column(string $column_name): self
    {
        $this->column_name = $column_name;
        $this->sql['columns'][$column_name] = [];
        return $this;
    }

    public function int(int $size = 255): self
    {
        $this->sql['columns'][$this->column_name]['int'] = "INT($size)";
        return $this;
    }

    public function bigInt(int $size = 255): self
    {
        $this->sql['columns'][$this->column_name]['bigInt'] = "BIGINT($size)";
        return $this;
    }

    public function double(int $size = 255, int $d = 2): self
    {
        $this->sql['columns'][$this->column_name]['double'] = "DOUBLE($size, $d)";
        return $this;
    }

    public function boolean(): self
    {
        $this->sql['columns'][$this->column_name]['boolean'] = "BOOLEAN";
        return $this;
    }

    public function string(int $size = 255): self
    {
        $this->sql['columns'][$this->column_name]['string'] = "VARCHAR($size)";
        return $this;
    }

    public function text(int $size = 10000): self
    {
        $this->sql['columns'][$this->column_name]['text'] = "TEXT($size)";
        return $this;
    }

    public function longText(int $size): self
    {
        $this->sql['columns'][$this->column_name]['longText'] = "LONGTEXT($size)";
        return $this;
    }

    public function required(): self
    {
        $this->sql['columns'][$this->column_name]['required'] = "NOT NULL";
        return $this;
    }

    public function primary($columns = []): self
    {
        if (! empty($columns)) {
            $this->sql['primary'] = "PRIMARY KEY (" . implode(',', $columns) . ")";
            return $this;
        }
        $this->sql['columns'][$this->column_name]['primary'] = "PRIMARY KEY";
        return $this;
    }

    public function index(array $columns): self
    {
        $this->sql['index'] = "INDEX (" . implode(',', $columns) . ")";
        return $this;
    }

    public function date(): self
    {
        $this->sql['columns'][$this->column_name]['date'] = "DATE";
        return $this;
    }

    public function dateTime(): self
    {
        $this->sql['columns'][$this->column_name]['dateTime'] = "DATETIME";
        return $this;
    }

    public function unsigned(): self
    {
        $this->sql['columns'][$this->column_name]['unsigned'] = "UNSIGNED";
        return $this;
    }

    public function autoIncrement(): self
    {
        $this->sql['columns'][$this->column_name]['autoIncrement'] = "AUTO_INCREMENT";
        return $this;
    }

    public function default($value): self
    {
        $val = is_string($value) ? "'$value'" : $value;
        $this->sql['columns'][$this->column_name]['default'] = "DEFAULT $val";
        return $this;
    }

    public function foreignKey(string $column, string $reference_table, string $reference_column): self
    {
        $table_name = $this->wpdb_object->prefix . $reference_table;
        $this->sql['foreignKey'] = "FOREIGN KEY ($column) REFERENCES $table_name ($reference_column)" ;
        return $this;
    }

    protected function start()
    {
        $this->sql['start'] = 'CREATE TABLE';
    }

    protected function get_table_name()
    {
        return $this->wpdb_object->prefix . $this->table_name;
    }

    public function execute()
    {
        $this->start();
        $query = SqlGenerator::create($this->sql);
        // return dump($query);

        $conn = $this->db;
        try {
            $conn->exec($query);
        } catch (\PDOException $exception) {
            $error_msg = sprintf(
                "<strong style='color: red;'>%s</strong>",
                $exception->getMessage()
            );
            throw new \Exception($error_msg);
        }
    }
}

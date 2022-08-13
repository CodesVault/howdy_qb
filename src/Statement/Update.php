<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\UpdateInterface;
use CodesVault\Howdyqb\SqlGenerator;

class Update implements UpdateInterface
{
    protected $db;
    protected $data = [];
    public $sql = [];
    protected $params = [];
    protected $table_name;
    protected $wpdb_object;

    public function __construct($db, string $table_name, array $data)
    {
        global $wpdb;
        $this->wpdb_object = $wpdb;

        $this->db = $db;
        $this->data = $data;
        $this->table_name = $this->wpdb_object->prefix . $table_name;
        $this->sql['set_columns'] = $this->set_columns();
    }

    private function update_data()
    {
        $query = SqlGenerator::update($this->sql);
        // return dump($this->params);

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

    public function execute()
    {
        $this->start();
        $this->update_data();
    }

    protected function start()
    {
        $this->sql['start'] = 'UPDATE ' . $this->table_name;
    }

    protected function set_columns()
    {
        if (empty($this->data)) return;

        $columns = [];
        foreach ($this->data as $column => $value) {
            $columns[] = $column . '=?';
            $this->params[] = $value;
        }
        return 'SET ' . implode(', ', $columns);
    }

    public function where($column, string $operator = null, string $value = null): self
    {
        if ( is_callable( $column ) ) {
            call_user_func( $column, $this );
            return $this;
        }
        $this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . ' ?';
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $operator = null, string $value = null): self
    {
        $this->sql['andWhere'] = 'AND ' . $column . ' ' . $operator . ' ?';
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator = null, string $value = null): self
    {
        $this->sql['orWhere'] = 'OR ' . $column . ' ' . $operator . ' ?';
        $this->params[] = $value;
        return $this;
    }
}

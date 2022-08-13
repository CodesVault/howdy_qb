<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\DeleteInterface;
use CodesVault\Howdyqb\SqlGenerator;

class Delete implements DeleteInterface
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

    protected function start()
    {
        $this->sql['start'] = 'DELETE FROM ' . $this->table_name;
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

    public function drop()
    {
        $this->sql['drop'] = 'DROP TABLE ' . $this->table_name;
        return $this;
    }

    private function delete_data()
    {
        $query = SqlGenerator::delete($this->sql);
        // return dump($query);

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
        $this->delete_data();
    }
}

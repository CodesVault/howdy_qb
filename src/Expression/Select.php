<?php

namespace CodesVault\WPqb\Expression;

use CodesVault\WPqb\SqlGenerator;

class Select
{
    protected $db;
    protected $sql = [];
    protected $params = [];
    protected $table_name;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function start()
    {
        $this->sql['start'] = 'SELECT';
    }

    public function distinct(): self
    {
        $this->sql['distinct'] = 'DISTINCT';
        return $this;
    }

    public function columns(...$columns): self
    {
        $this->sql['columns'] = implode(', ', $columns);
        return $this;
    }

    public function from(string $table_name): self
    {
        global $wpdb;
        $this->sql['table_name'] = 'FROM ' . $wpdb->prefix . $table_name;
        return $this;
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

    public function andWhere($column, string $operator = null, string $value = null): self
    {
        if ( is_callable( $column ) ) {
            call_user_func( $column, $this );
            return $this;
        }
        $this->sql['andWhere'] = 'AND ' . $column . ' ' . $operator . ' ?';
        $this->params[] = $value;
        return $this;
    }

    public function orWhere($column, string $operator = null, string $value = null): self
    {
        if ( is_callable( $column ) ) {
            call_user_func( $column, $this );
            return $this;
        }
        $this->sql['orWhere'] = 'OR ' . $column . ' ' . $operator . ' ?';
        $this->params[] = $value;
        return $this;
    }

    private function fetch($query, array $args = [])
    {
        $conn = $this->db;
        $data = $conn->prepare($query);
        $data->execute($args);
        return $data->fetchAll(\PDO::FETCH_ASSOC);
    }

    // get data from database
    public function get()
    {
        $this->start();
        $query = SqlGenerator::run($this->sql);
        // return $query;

        $data = $this->fetch($query, $this->params);
        return $data;
    }
}

<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\UpdateInterface;
use CodesVault\Howdyqb\QueryFactory;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;

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
        $this->wpdb_object = QueryFactory::getConfig();
        if (empty(QueryFactory::getConfig())) {
            global $wpdb;
            $this->wpdb_object = $wpdb;
        }

        $this->db = $db;
        $this->data = $data;
        $this->table_name = $this->wpdb_object->prefix . $table_name;
        $this->sql['set_columns'] = $this->set_columns();
    }

    private function driver_exicute($sql)
    {
        $driver = $this->db;
        if ('wpdb' === QueryFactory::getDriver()) {
            return $driver->query($driver->prepare($sql, $this->params));
        }

        $data = $driver->prepare($sql);
        return $data->execute($this->params);
    }

    private function update_data()
    {
        $query = SqlGenerator::update($this->sql);

        try {
            $this->driver_exicute($query);
        } catch (\Exception $exception) {
            Utilities::throughException($exception);
        }
    }

    public function execute()
    {
        $this->start();
        $this->update_data();
    }

    // get only sql query string
    public function getSql()
    {
        $this->start();
        $query = [
            'query'     => SqlGenerator::update($this->sql),
            'params'    => $this->params,
        ];
        return $query;
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
            $columns[] = $column . '=' .  Utilities::get_placeholder();
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
        $this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder();
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $operator = null, string $value = null): self
    {
        $this->sql['andWhere'] = 'AND ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder();
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator = null, string $value = null): self
    {
        $this->sql['orWhere'] = 'OR ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder();
        $this->params[] = $value;
        return $this;
    }
}

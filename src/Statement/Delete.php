<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\DeleteInterface;
use CodesVault\Howdyqb\QueryFactory;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;

class Delete implements DeleteInterface
{
    protected $db;
    public $sql = [];
    protected $params = [];
    protected $table_name;

    public function __construct($db, string $table_name)
    {
        $this->db = $db;
        $this->table_name = Utilities::get_db_configs()->prefix . $table_name;
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
        $this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $operator = null, string $value = null): self
    {
        $this->sql['andWhere'] = 'AND ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator = null, string $value = null): self
    {
        $this->sql['orWhere'] = 'OR ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function drop()
    {
        $this->sql['drop'] = 'DROP TABLE ' . $this->table_name;
        return $this;
    }

    public function dropIfExists()
    {
        $this->sql['drop'] = 'DROP TABLE IF EXISTS ' . $this->table_name;
        return $this;
    }

    private function driver_exicute($sql)
    {
        $driver = $this->db;
        if (class_exists('wpdb') && $driver instanceof \wpdb) {
            return $driver->query($driver->prepare($sql, $this->params));
        }

        $data = $driver->prepare($sql);
		try {
			return $data->execute($this->params);
        } catch (\PDOException $exception) {
            Utilities::throughException($exception);
        }
    }

    private function delete_data()
    {
        $query = SqlGenerator::delete($this->sql);

        return $this->driver_exicute($query);
    }

    // get only sql query string
    public function getSql()
    {
        $this->start();
        $query = [
            'query'     => SqlGenerator::delete($this->sql),
            'params'    => $this->params,
        ];
        return $query;
    }

    public function execute()
    {
        $this->start();
        $this->delete_data();
    }
}

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

    public function __construct($db, string $table_name, array $data)
    {
		$this->db = $db;

        $this->data = $data;
        $this->table_name = $this->get_table_prefix()->prefix . $table_name;
        $this->sql['set_columns'] = $this->set_columns();
    }

    private function driver_execute($sql)
    {
        $driver = $this->db;
        if (class_exists('wpdb') && $driver instanceof \wpdb) {
            return $driver->query($driver->prepare($sql, $this->params));
        }

        $data = $driver->prepare($sql);
		try {
			return $data->execute($this->params);
        } catch (\Exception $exception) {
            Utilities::throughException($exception);
        }
    }

    private function update_data()
    {
        $query = SqlGenerator::update($this->sql);

        $this->driver_execute($query);
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

	private function get_table_prefix()
	{
        if (empty(QueryFactory::getConfig())) {
            global $wpdb;
            return $wpdb;
        }
		return QueryFactory::getConfig();
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
            $columns[] = $column . '=' .  Utilities::get_placeholder($this->db, $value);
            $this->params[] = $value;
        }
        return 'SET ' . implode(', ', $columns);
    }

    public function where($column, string $operator = null, $value = null): self
    {
        if ( is_callable( $column ) ) {
            call_user_func( $column, $this );
            return $this;
        }
        $this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $operator = null, $value = null): self
    {
        $this->sql['andWhere'] = 'AND ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator = null, $value = null): self
    {
        $this->sql['orWhere'] = 'OR ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }
}

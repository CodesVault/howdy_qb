<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\AlterInterface;
use CodesVault\Howdyqb\QueryFactory;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;

class Alter implements AlterInterface
{
    protected $db;
    public $sql = [];
    protected $params = [];
    protected $column_name;
    protected $connection_instence;
    protected $table_name;

    public function __construct($db, string $table_name)
    {
        $this->db = $db;
        $this->table_name = $table_name;
        $this->connection_instence = QueryFactory::getConfig();
        if (empty(QueryFactory::getConfig())) {
            global $wpdb;
            $this->connection_instence = $wpdb;
        }

        $this->start();
    }

	public function add(string $column): self
	{
		$this->sql[$column] = "ADD $column";
		return $this;
	}

	public function modify(string $old_column, string $new_column = ''): self
	{
		$this->column_name = $new_column;
		$this->sql[$new_column] = trim("MODIFY COLUMN $old_column $new_column");
		return $this;
	}

	public function drop(string $column): self
	{
		$this->sql[$column] = "DROP COLUMN $column";
		return $this;
	}

    public function int(int $size = 255): self
    {
        $this->sql[$this->column_name] .= " INT($size)";
        return $this;
    }

    public function bigInt(int $size = 255): self
    {
        $this->sql[$this->column_name] .= " BIGINT($size)";
        return $this;
    }

    public function double(int $size = 255, int $d = 2): self
    {
        $this->sql[$this->column_name] .= " DOUBLE($size, $d)";
        return $this;
    }

    public function boolean(): self
    {
        $this->sql[$this->column_name] .= " BOOLEAN";
        return $this;
    }

    public function string(int $size = 255): self
    {
        $this->sql[$this->column_name] .= " VARCHAR($size)";
        return $this;
    }

    public function text(int $size = 10000): self
    {
        $this->sql[$this->column_name] .= " TEXT($size)";
        return $this;
    }

    public function longText(int $size): self
    {
        $this->sql[$this->column_name] .= " LONGTEXT($size)";
        return $this;
    }

    public function required(): self
    {
        $this->sql[$this->column_name] .= " NOT NULL";
        return $this;
    }

    public function primary($columns = []): self
    {
        if (! empty($columns)) {
            $this->sql['primary'] = "PRIMARY KEY (" . implode(',', $columns) . ")";
            return $this;
        }
        $this->sql[$this->column_name] .= " PRIMARY KEY";
        return $this;
    }

    public function index(array $columns): self
    {
        $this->sql['index'] = "INDEX (" . implode(',', $columns) . ")";
        return $this;
    }

    public function date(): self
    {
        $this->sql[$this->column_name] .= " DATE";
        return $this;
    }

    public function dateTime(): self
    {
        $this->sql[$this->column_name] .= " DATETIME";
        return $this;
    }

    public function unsigned(): self
    {
        $this->sql[$this->column_name] .= " UNSIGNED";
        return $this;
    }

    public function autoIncrement(): self
    {
        $this->sql[$this->column_name] .= " AUTO_INCREMENT";
        return $this;
    }

    public function default($value): self
    {
        $val = is_string($value) ? "'$value'" : $value;
        $this->sql[$this->column_name] .= " DEFAULT $val";
        return $this;
    }

    protected function start()
    {
		$table_name = $this->get_table_name();
        $this->sql['start'] = "ALTER TABLE $table_name";
    }

    protected function get_table_name()
    {
        return $this->connection_instence->prefix . $this->table_name;
    }

    // get only sql query string
    public function getSql()
    {
        $this->start();
        $query = [
            'query' => SqlGenerator::alter($this->sql),
        ];
        return $query;
    }

    private function driver_exicute($sql)
    {
        $driver = $this->db;
        if ('wpdb' === QueryFactory::getDriver()) {
            return $driver->query($sql);
        }

        return $driver->exec($sql);
    }

    public function execute()
    {
        $this->start();
        $query = SqlGenerator::alter($this->sql);

        try {
            $this->driver_exicute($query);
        } catch (\PDOException $exception) {
            Utilities::throughException($exception);
        }
    }
}

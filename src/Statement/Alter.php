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
    protected $table_name;
    protected $column_exists = false;

    public function __construct($db, string $table_name)
    {
        $this->db = $db;
        $this->table_name = $table_name;

        $this->start();
    }

	public function add(string $column): self
	{
	    $this->column_name = $column;
        if ($this->hasColumn($column)) {
           $this->column_exists = true;
        }

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

    public function double(): self
    {
        $this->sql[$this->column_name] .= " DOUBLE";
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

	public function json(): self
    {
        $this->sql[$this->column_name] .= " JSON";
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

	public function timestamp($default = null, $on_update = null): self
	{
		$this->sql[$this->column_name] .= " TIMESTAMP";

		if ($default === 'now') {
			$this->sql[$this->column_name] .= " DEFAULT CURRENT_TIMESTAMP";
		} elseif ($default && $default !== 'now') {
			$this->sql[$this->column_name] .= " DEFAULT " . $default;
		}

		if ($on_update === 'current') {
			$this->sql[$this->column_name] .= " ON UPDATE CURRENT_TIMESTAMP";
		} elseif ($on_update && $on_update !== 'current') {
			$this->sql[$this->column_name] .= " ON UPDATE " . $on_update;
		}

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

	public function nullable(): self
    {
        return $this->default('NULL');
    }

    public function foreignKey(string $column, string $reference_table, string $reference_column): self
    {
        $table_name = Utilities::get_db_configs()->prefix . $reference_table;
        $this->sql['foreignKey'] = "ADD FOREIGN KEY (`$column`) REFERENCES $table_name (`$reference_column`)" ;
        return $this;
    }

    public function onDelete(string $action): self
    {
        $this->sql['onDelete'] = "ON DELETE $action";
        return $this;
    }

	public function enum(array $allowed): self
	{
		$list = '';
		foreach ($allowed as $value) {
			if (gettype($value) === 'string') {
				$list .= "'$value', ";
			} else {
				$list .= $value . ", ";
			}
		}

		$list = substr(trim($list), 0, -1);

		$this->sql[$this->column_name] .= " ENUM(" . $list . ")";
		return $this;
	}

	public function decimal(int $precision = 8, int $scale = 2): self
	{
		$this->sql[$this->column_name] .= " DECIMAL($precision, $scale)";
		return $this;
	}

	public function float(): self
	{
		$this->sql[$this->column_name] .= " FLOAT";
		return $this;
	}

    protected function start()
    {
		$table_name = $this->get_table_name();
        $this->sql['start'] = "ALTER TABLE $table_name";
    }

    private function get_table_name()
    {
       return $this->getTablePrefix()->prefix . $this->table_name;
    }

	private function getTablePrefix()
	{
        if (empty(QueryFactory::getConfig())) {
            global $wpdb;
            return $wpdb;
        }
		return QueryFactory::getConfig();
	}

	private function hasColumn($column)
    {
        $columns = [];
        $driver = $this->db;
        $table_name = Utilities::get_db_configs()->prefix .$this->table_name;

        if (class_exists('wpdb') && $driver instanceof \wpdb) {
            $columns = $driver->get_results("DESCRIBE `$table_name`", ARRAY_N);
        } else {
            $columns = $driver->query("DESCRIBE `$table_name`")->fetchAll(\PDO::FETCH_COLUMN);
        }

        foreach ($columns as $value) {
            if (is_array($value) && $value[0] === $column) {
                return $column;
            }
            if (! is_array($value) && $value === $column) {
                return $column;
            }
        }

        return false;
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

    private function driver_execute($sql)
    {
        $driver = $this->db;
        if (class_exists('wpdb') && $driver instanceof \wpdb) {
            return $driver->query($sql);
        }

		try {
			return $driver->exec($sql);
        } catch (\PDOException $exception) {
            Utilities::throughException($exception);
        }
    }

    public function execute()
    {
        $this->start();
        $query = SqlGenerator::alter($this->sql);

        if ($this->column_exists) {
            return;
        }

    	$this->driver_execute($query);
    }
}

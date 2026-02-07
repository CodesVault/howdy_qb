<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Expression\SqlCore;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;
use CodesVault\Howdyqb\Validation\IdentifierValidator;

class Insert
{
	// bring all SQL expressions
	use SqlCore;

    protected $db;
    protected $data = [];
	protected $insert_sql = [];
    protected $sql = [];
    public $test = [];
    protected $params = [];
    private $table_name;

    public function __construct($db, string $table_name, array $data)
    {
        $this->db = $db;
        $this->data = $data;
        $this->table_name = IdentifierValidator::validateTableName($table_name);

        $this->start();
        $this->insert_sql['insert_table_name'] = $this->getTableName();
        $this->insert_sql['insert_columns'] = $this->getColumns();
        $this->insert_sql['value_placeholders'] = $this->getValuePlaceholders();
        $this->params = $this->getParams();
    }

    private function driverExecute($sql)
    {
        $driver = $this->db;
        if (class_exists('wpdb') && $driver instanceof \wpdb) {
            return $driver->query($driver->prepare($sql, $this->params));
        }

		try {
			$data = $driver->prepare($sql);
			return $data->execute($this->params);
        } catch (\Exception $exception) {
            Utilities::throughException($exception);
        }
    }

    private function start()
    {
        $this->insert_sql['start'] = 'INSERT INTO';
    }

    private function getTableName()
    {
        $prefix = Utilities::get_db_configs()->prefix;
        return IdentifierValidator::escapeIdentifier($prefix . $this->table_name);
    }

    private function getColumns()
    {
        if (empty($this->data)) return;

		if (! is_array($this->data[0])) {
			// Validate and escape column names for INSERT...SELECT
			$escapedColumns = array_map(function ($col) {
				return IdentifierValidator::validateColumnName($col);
			}, $this->data);
			return '(' . implode(', ', $escapedColumns) . ')';
		}

        $columns = IdentifierValidator::validateColumnNames(array_keys($this->data[0]));

        return '(' . implode(', ', $columns) . ')';
    }

    private function getValuePlaceholders()
    {
		if (empty($this->data) || ! is_array($this->data[0])) return;

        $placeholders = [];

        if (count($this->data) > 1) {
            foreach ($this->data as $row) {
                $placeholders[] = '(' . implode(',', array_fill(0, count($row), Utilities::get_placeholder($this->db, $row))) . ')';
            }
        } else {
            $placeholders[] = '(' . implode(',', array_fill(0, count($this->data[0]), Utilities::get_placeholder($this->db, $this->data))) . ')';
        }
        return 'VALUES ' . implode(',', $placeholders);
    }

    private function getParams()
    {
		if (empty($this->data) || ! is_array($this->data[0])) return [];

        $params = [];
        foreach ($this->data as $value) {
            foreach ($value as $val) {
                $params[] = $val;
            }
        }
        return $params;
    }

	public function ignoreDuplicates(): self
	{
		$this->insert_sql['start'] = 'INSERT IGNORE INTO';
		return $this;
	}

	public function select(...$columns): self
	{
		$this->sql['start']['select'] = 'SELECT';
		if (! empty($columns)) {
			$this->columns(...$columns);
			return $this;
		}

		return $this;
	}

	public function getSql()
	{
		$this->setStartExpression();
		return [
			'query'     => SqlGenerator::insert($this->insert_sql, $this->sql),
			'params'    => $this->params,
		];
	}

	public function execute()
	{
		$this->setStartExpression();
		$query = SqlGenerator::insert($this->insert_sql, $this->sql);
        return $this->driverExecute($query);
	}
}

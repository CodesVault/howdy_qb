<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\TableInterface;
use CodesVault\Howdyqb\Utilities;
use CodesVault\Howdyqb\Validation\IdentifierValidator;

class Table implements TableInterface
{
	protected $db;
	protected $params = [];
	private $table_name;

	public function __construct($db, string $table_name)
	{
		$this->db = $db;
		$prefix = Utilities::get_db_configs()->prefix;
		$this->table_name = IdentifierValidator::validateTableName($prefix . $table_name);
	}

	public function drop()
	{
		$sql = 'DROP TABLE ' . $this->table_name;
		$this->driverExecute($sql);
	}

	public function dropIfExists()
	{
		$sql = 'DROP TABLE IF EXISTS ' . $this->table_name;
		$this->driverExecute($sql);
	}

	public function truncate()
	{
		$sql = 'TRUNCATE TABLE ' . $this->table_name;
		$this->driverExecute($sql);
	}

	private function driverExecute($sql)
	{
		$driver = $this->db;
		if (class_exists('wpdb') && $driver instanceof \wpdb) {
			return $driver->query($sql);
		}

		$data = $driver->prepare($sql);
		try {
			return $data->execute($this->params);
		} catch (\Exception $exception) {
			Utilities::throughException($exception);
		}
	}
}

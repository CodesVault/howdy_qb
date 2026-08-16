<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\TableInterface;
use CodesVault\Howdyqb\QueryFactory;
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

	public function hasTable(string $table_name): bool
	{
		$configs = Utilities::get_db_configs();
		$full_table_name = $configs->prefix . $table_name;
		$driver = $this->db;

		if ('pdo' === QueryFactory::getDriver()) {
			$sql = 'SELECT EXISTS (
				SELECT 1 FROM information_schema.TABLES
				WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
			)';

			try {
				$data = $driver->prepare($sql);
				$data->execute([$configs->dbname, $full_table_name]);
				return (bool) $data->fetchColumn();
			} catch (\Exception $exception) {
				Utilities::throughException($exception);
			}
		}

		$sql = 'SELECT EXISTS (
			SELECT 1 FROM information_schema.TABLES
			WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s
		)';

		return (bool) $driver->get_var(
			$driver->prepare($sql, $configs->dbname, $full_table_name)
		);
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

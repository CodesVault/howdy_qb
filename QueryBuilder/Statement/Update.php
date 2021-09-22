<?php
/**
 * Update statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Statement;

use WPQB\QueryBuilder\BluPrint;


class Update extends BluPrint {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb) {
		static::$db = $wpdb;
	}

	private function callback(Update $db) {
		$db;
	}

	public function tableName($table_name) {
		if ( ! $table_name || ! is_string( $table_name ) ) throw new \Exception('Not a valid query.');

		$prefix = static::$db->prefix;
		static::$query_string = "UPDATE {$prefix}{$table_name}";
		return $this;
	}

	public function set($fields) {
		if ( ! $fields || ! is_string( $fields ) ) throw new \Exception('Not a valid query.');

		static::$query_string = static::$query_string . " SET {$fields}";
		return $this;
	}

	public function renovate($args = []) {
		if ( ! static::$query_string ) throw new \Exception('No query found.');

		// return static::$query_string;
		return $this->query(static::$query_string, $args);
	}

}

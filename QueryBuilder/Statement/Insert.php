<?php
/**
 * Insert statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Statement;

use WPQB\QueryBuilder\BluPrint;


class Insert extends BluPrint {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb) {
		static::$db = $wpdb;
	}

	private function callback(Insert $db) {
		return $db;
	}

	public function into($table_name) {
		if ( ! $table_name || ! is_string( $table_name ) ) throw new \Exception('Not a valid query.');

		$table = static::$db->prefix . $table_name;
		static::$query_string = "INSERT INTO {$table}";
		return $this;
	}

	public function insertColumn($col_name) {
		if ( ! $col_name || ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		static::$query_string = static::$query_string . " (" . $col_name . ")";
		return $this;
	}

	public function value($value) {
		$arr = explode( ', ', $value );
		preg_match_all( '/(%s)|(%d)/', $value, $matches );
		if ( count( $arr ) != count( $matches[0] ) ) {
			print_r( "{$arr} {$matches} Value placeholders are not valid." );
			return $this;
		}; 

		static::$query_string = static::$query_string . " VALUES (" . $value . ")";
		return $this;
	}

	public function add($args = []) {
		if ( ! static::$query_string ) throw new \Exception('No query found.');

		// return static::$query_string;
		return $this->query(static::$query_string, $args);
	}

}

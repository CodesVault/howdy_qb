<?php
/**
 * Insert statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Add;

use WPQB\QueryBuilder\Get\Select;


class Insert extends Select {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb) {
		static::$db = $wpdb;
	}

	public function into($table_name) {
		if ( ! $table_name || ! is_string( $table_name ) ) throw new \Exception('Not a valid query.');

		$table = static::$db->prefix . $table_name;
		static::$query_string = "INSERT INTO {$table}";
		return $this;
	}

	public function column($col_name) {
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

	// public function select($column = "*", $distinct = false) {
	// 	if ( ! $column || ! is_string( $column ) ) throw new \Exception('Not a valid query.');

	// 	if ( $distinct ) {
	// 		static::$query_string = static::$query_string . " SELECT DISTINCT {$column}";
	// 		return $this;
	// 	}
	// 	static::$query_string = static::$query_string . " SELECT {$column}";
	// 	return $this;
	// }

	private function query($query, $args = []) {
		$db = static::$db;
		return $db->query( $db->prepare( $query, $args ) );
	}

	public function add($args = []) {
		if ( ! static::$query_string ) throw new \Exception('No query found.');

		// return static::$query_string;
		return $this->query(static::$query_string, $args);
	}

}

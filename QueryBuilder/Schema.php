<?php
/**
 * Schema class.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder;


class Schema {

	protected static $db;
	protected static $query_string;
	protected static $table_name;
	protected static $column_structure;

	// public function int($col_name, $size, $default = null) {
	// 	if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

	// 	$query = static::$query_string;
	// 	static::$query_string = "{$query} " . $col_name . " int(" . $size . ")";
	// 	if ( $default ) static::$query_string . " DEFAULT " . $default; 
	// }

	public function table($table_name) {
		if ( ! is_string( $table_name ) ) throw new \Exception('Not a valid query.');

		$table = static::$db->prefix . $table_name;
		static::$table_name = $table;
		static::$query_string = "CREATE TABLE " . $table . " ( ";
		return $this;
	}

	public function bigInt($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		// $query = static::$query_string;
		// static::$query_string = "{$query} " . $col_name . " bigint(" . $size . ")";
		// if ( $default ) static::$query_string . " DEFAULT " . $default; 

		$col_struct = "bigint(" . $size . ")";
		if ( $default ) $col_struct . " DEFAULT " . $default; 
		static::$column_structure[$col_name] = $col_struct;

		return $this;
	}

	public function float($col_name, $size, $d, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		// static::$query_string = "{$query} " . $col_name . " float(" . $size . ", " . $d . ")";
		// if ( $default ) static::$query_string . " DEFAULT " . $default;

		$col_struct = "float(" . $size . ", " . $d . ")";
		if ( $default ) $col_struct . " DEFAULT " . $default;
		static::$column_structure[$col_name] = $col_struct;

		return $this;
	}

	public function increments($col_name) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$col_struct = "bigint(20) AUTO_INCREMENT";
		static::$column_structure[$col_name] = $col_struct;
		// static::$query_string = "{$query} " . $col_name . " bigint(20) AUTO_INCREMENT,";
		return $this;
	}

	public function primary_key() {
		$column_key = array_key_last(static::$column_structure);
		$col_value = static::$column_structure[$column_key];
		static::$column_structure[$column_key] = $col_value . " PRIMARY KEY";
		
		return $this;
	}

	public function unique() {
		$last_column = array_key_last(static::$column_structure);
		$last_column . " UNIQUE";
		return $this;
	}

	public function varchar($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		// $query = static::$query_string;
		// static::$query_string = " varchar(" . $size . ")";
		// if ( $default ) static::$query_string . " DEFAULT " . $default;

		$col_struct = "varchar(" . $size . ")";
		if ( $default ) $col_struct . " DEFAULT " . $default;
		static::$column_structure[$col_name] = $col_struct;

		return $this;
	}

	public function char($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		// $query = static::$query_string;
		// static::$query_string = "{$query} " . $col_name . " char(" . $size . ")";
		// if ( $default ) static::$query_string . " DEFAULT " . $default;

		$col_struct = "char(" . $size . ")";
		if ( $default ) $col_struct . " DEFAULT " . $default;
		static::$column_structure[$col_name] = $col_struct;

		return $this;
	}

	public function text($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		// $query = static::$query_string;
		// static::$query_string = "{$query} " . $col_name . " text(" . $size . ")";
		// if ( $default ) static::$query_string . " DEFAULT " . $default;

		$col_struct = "text(" . $size . ")";
		if ( $default ) $col_struct . " DEFAULT " . $default;
		static::$column_structure[$col_name] = $col_struct;

		return $this;
	}

	public function dateTime($col_name, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		// $query = static::$query_string;
		// static::$query_string = "{$query} " . $col_name . " DATETIME";
		// if ( $default ) static::$query_string . " DEFAULT " . $default;

		$col_struct = "DATETIME";
		if ( $default ) $col_struct . " DEFAULT " . $default;
		static::$column_structure[$col_name] = $col_struct;

		return $this;
	}

}

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

	public function int($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " int(" . $size . ")";
		if ( $default ) static::$query_string . " DEFAULT " . $default; 
	}

	public function bigInt($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " bigint(" . $size . ")";
		if ( $default ) static::$query_string . " DEFAULT " . $default; 
	}

	public function float($col_name, $size, $d, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " float(" . $size . ", " . $d . ")";
		if ( $default ) static::$query_string . " DEFAULT " . $default;
	}

	public function increments($col_name) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " bigint(20) AUTO_INCREMENT";
	}

	public function unique() {
		return static::$query_string . " UNIQUE";
	}

	public function varchar($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " varchar(" . $size . ")";
		if ( $default ) static::$query_string . " DEFAULT " . $default;
	}

	public function char($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " char(" . $size . ")";
		if ( $default ) static::$query_string . " DEFAULT " . $default;
	}

	public function text($col_name, $size, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " text(" . $size . ")";
		if ( $default ) static::$query_string . " DEFAULT " . $default;
	}

	public function dateTime($col_name, $default = null) {
		if ( ! is_string( $col_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} " . $col_name . " DATETIME";
		if ( $default ) static::$query_string . " DEFAULT " . $default;
	}

}

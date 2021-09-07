<?php
/**
 * Select statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Get;


class Select {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb, $column = "*", $distinct = false) {
		static::$db = $wpdb;
		$this->select($column, $distinct);
	}

	private function select($column = "*", $distinct = false) {
		if ( $distinct ) {
			static::$query_string = "SELECT DISTINCT {$column}";
			return static::$query_string;
		}
		static::$query_string = "SELECT {$column}";
		return static::$query_string;
	}

	public function from($table_name) {
		$table = static::$db->prefix . $table_name;
		$query = static::$query_string;
		static::$query_string = "{$query} FROM {$table}";

		return $this;
	}

	public function where($where) {
		$query = static::$query_string;
		static::$query_string = "{$query} WHERE {$where}";
		return $this;
	}

	public function and($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} AND {$condition}";
		return $this;
	}

	public function in($in) {
		$query = static::$query_string;
		static::$query_string = "{$query} IN({$in})";
		return $this;
	}

	public function between($between) {
		$query = static::$query_string;
		static::$query_string = "{$query} between {$between}";
		return $this;
	}

	public function or($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} OR {$condition}";
		return $this;
	}

	public function groupBy($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} GROUP BY {$condition}";
		return $this;
	}

	public function orderBy($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} ORDER BY {$condition}";
		return $this;
	}

	public function limit($limit) {
		if ( ! is_int( $limit ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} LIMIT {$limit}";
		return $this;
	}

	public function offset($offset) {
		$query = static::$query_string;
		static::$query_string = "{$query} OFFSET {$offset}";
		return $this;
	}

	public function join($table_name) {
		$query = static::$query_string;
		$prefix = static::$db->prefix;
		static::$query_string = "{$query} JOIN {$prefix}{$table_name}";
		return $this;
	}

	public function on($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} ON {$condition}";
		return $this;
	}

	private function query($query, $args = []) {
		$db = static::$db;
		return $db->get_results( $db->prepare( $query, $args ) );
	}

	public function get($args = []) {
		if ( ! static::$query_string ) throw new \Exception('Not a valid query.');

		// return static::$query_string;
		return $this->query(static::$query_string, $args);
	}

}

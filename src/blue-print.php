<?php
/**
 * Query Blue print class.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder;


class BluPrint {

	protected static $db;
	protected static $query_string;

	public function select($column = "*", $distinct = false) {
		if ( ! $column || ! is_string( $column ) ) throw new \Exception('Not a valid query.');

		if ( $distinct ) {
			static::$query_string = static::$query_string . " SELECT DISTINCT {$column}";
			return $this;
		}
		static::$query_string = static::$query_string . " SELECT {$column}";
		return $this;
	}

	public function column($column_name, $unique = false) {
		if ( ! is_string( $column_name ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} SELECT " . $column_name;
		$unique ? static::$query_string = "{$query} SELECT DISTINCT " . $column_name : static::$query_string;

		if ( ! $query ) {
			static::$query_string = "SELECT " . $column_name;
			$unique ? static::$query_string = "{$query} SELECT DISTINCT " . $column_name : static::$query_string;
		}
		return $this;
	}

	public function from($table_name) {
		if ( ! is_string( $table_name ) ) throw new \Exception('Not a valid query.');

		$table = static::$db->prefix . $table_name;
		$query = static::$query_string;
		static::$query_string = "{$query} FROM {$table}";

		return $this;
	}

	public function where($where) {
		if ( ! is_string( $where ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} WHERE {$where}";
		return $this;
	}

	public function and($condition) {
		if ( ! is_string( $condition ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} AND {$condition}";
		return $this;
	}

	public function in($in) {
		$query = static::$query_string;
		static::$query_string = "{$query} IN({$in})";
		return $this;
	}

	public function between($start, $end) {
		$query = static::$query_string;
		static::$query_string = "{$query} BETWEEN {$start} AND {$end}";
		return $this;
	}

	public function or($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} OR {$condition}";
		return $this;
	}

	public function not($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} NOT {$condition}";
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
		if ( ! is_int( $offset ) ) throw new \Exception('Not a valid query.');

		$query = static::$query_string;
		static::$query_string = "{$query} OFFSET {$offset}";
		return $this;
	}

	public function join($table_name, $type = "inner") {
		$join_type = $type;
		if ( $type === "inner" ) {
			$join_type = strtoupper($type);
		} elseif ( $type === "left" ) {
			$join_type = strtoupper($type);
		} elseif ( $type === "right" ) {
			$join_type = strtoupper($type);
		}

		$query = static::$query_string;
		$prefix = static::$db->prefix;
		static::$query_string = "{$query} {$join_type} JOIN {$prefix}{$table_name}";
		return $this;
	}

	public function on($condition) {
		$query = static::$query_string;
		static::$query_string = "{$query} ON {$condition}";
		return $this;
	}

	protected function query($query, $args = []) {
		$db = static::$db;
		return $db->get_results( $db->prepare( $query, $args ) );
	}

}

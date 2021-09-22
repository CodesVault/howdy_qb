<?php
/**
 * Select statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Statement;

use WPQB\QueryBuilder\BluPrint;


class Select extends BluPrint {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb) {
		static::$db = $wpdb;
	}

	private function callback(Select $db) {
		return $db;
	}

	public function get($args = []) {
		if ( ! static::$query_string ) throw new \Exception('Not a valid query.');

		// return static::$query_string;
		return $this->query(static::$query_string, $args);
	}

}

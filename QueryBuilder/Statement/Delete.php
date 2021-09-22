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


class Delete extends BluPrint {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb) {
		static::$db = $wpdb;
		static::$query_string = "DELETE";
	}
	
	public function remove($args = []) {
		if ( ! static::$query_string ) throw new \Exception('No query found.');

		// return static::$query_string;
		return $this->query(static::$query_string, $args);
	}

}

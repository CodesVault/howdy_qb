<?php
/**
 * Create statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Make;

use WPQB\QueryBuilder\Schema;


class Create extends Schema {

	protected static $db;
	protected static $query_string;

	function __construct($wpdb) {
		static::$db = $wpdb;
	}

	private function callback(Schema $db) {
		return $db;
	}

}

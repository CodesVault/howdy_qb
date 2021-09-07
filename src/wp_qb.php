<?php
/**
 * Query Builder main class.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder;

use WPQB\QueryBuilder\Get\Select;
use WPQB\QueryBuilder\Add\Insert;
use WPQB\QueryBuilder\Renovate\Update;
use WPQB\QueryBuilder\Remove\Delete;


class WPQuery {

	private static function connect() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * SELECT statement of mySql query.
	 * 
	 * @param string $column
	 * @param bool $distinct
	 * 
	 * @return \WPQB\QueryBuilder\Get\Select
	 */
	public static function select(string $column = '*', bool $distinct = false) {
		if ( ! $column ) throw new \Exception('Not a valid query.');

		return new Select(static::connect(), $column, $distinct);
	}

	/**
	 * INSERT statement of mySql query.
	 * 
	 * @return \WPQB\QueryBuilder\Add\Insert
	 */
	public static function insert() {
		return new Insert(static::connect());
	}

	/**
	 * UPDATE statement of mySql query.
	 * 
	 * @param string $table_name
	 * 
	 * @return \WPQB\QueryBuilder\Renovate\Update
	 */
	public static function update($table_name) {
		return new Update(static::connect(), $table_name);
	}

	/**
	 * DELETE statement of mySql query.
	 * 
	 * @param string $table_name
	 * 
	 * @return \WPQB\QueryBuilder\Remove\Delete
	 */
	public static function delete() {
		return new Delete(static::connect());
	}

	/**
	 * schema of a table.
	 * 
	 * @param string $table
	 * 
	 * @return array
	 */
	public static function schema($table) {
		if ( ! $table || ! is_string( $table ) ) throw new \Exception('Not a valid query.');
		
		$db = static::connect();
		return $db->get_results( $db->prepare( "DESCRIBE " . $db->prefix . $table, [] ) );
	}

}

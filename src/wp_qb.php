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
use WPQB\QueryBuilder\Make\Create;
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
	 * @param calllable $query {@see callback()}
	 * 
	 * @return mixed
	 */
	public static function select(callable $query) {
		$db = new Select(static::connect());
		return call_user_func( $query, $db );
	}

	/**
	 * INSERT statement of mySql query.
	 * 
	 * @param calllable $query {@see callback()}
	 * 
	 * @return mixed
	 */
	public static function insert(callable $query) {
		$db = new Insert(static::connect());
		return call_user_func( $query, $db );
	}

	/**
	 * UPDATE statement of mySql query.
	 * 
	 * @param calllable $query {@see callback()}
	 * 
	 * @return mixed
	 */
	public static function update(callable $query) {
		$db = new Update(static::connect());
		return call_user_func( $query, $db );
	}

	/**
	 * DELETE statement of mySql query.
	 * 
	 * @param calllable $query {@see callback()}
	 * 
	 * @return mixed
	 */
	public static function delete(callable $query) {
		$db = new Delete(static::connect());
		return call_user_func( $query, $db );
	}

	/**
	 * Create statement of mySql query.
	 * 
	 * @param calllable $query {@see callback()}
	 * 
	 * @return mixed
	 */
	public static function create(callable $query) {
		$db = new Create(static::connect());
		return call_user_func( $query, $db );
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

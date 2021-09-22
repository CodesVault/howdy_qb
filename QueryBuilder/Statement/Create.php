<?php
/**
 * Create statement queries.
 *
 * @link       https://abmsourav.com/
 *
 * @package    wp_qb
 * @author     abmSourav 
 */
namespace WPQB\QueryBuilder\Statement;

use WPQB\QueryBuilder\Schema;


class Create extends Schema {

	protected static $db;
	protected static $query_string;
	protected static $table_name;
	protected static $column_structure;

	function __construct($wpdb) {
		static::$db = $wpdb;
	}

	private function callback(Schema $db) {
		return $db;
	}

	private function table_exists() {
		$query = "SELECT table_name FROM information_schema.tables WHERE table_name = %s";
		return static::$db->query( static::$db->prepare( $query, static::$table_name ) );
	}

	private function col_struct() {
		$column_structure = "";
		$last_key = array_key_last(static::$column_structure);

		foreach ( static::$column_structure as $key => $struct ) {
			if ( $last_key === $key ) {
				$column_structure = $column_structure . $key . " " . $struct . " )";
			} else {
				$column_structure = $column_structure . $key . " " . $struct . ", ";
			}
		}
		return $column_structure;
	}

	public function make($args = []) {
		if ( ! static::$query_string ) throw new \Exception('Not a valid query.');
		if ( $this->table_exists() ) throw new \Exception('This table already exist.');

		$charset_collate = static::$db->get_charset_collate();
		$query_string = static::$query_string . $this->col_struct() . " " . $charset_collate;
		// return $query_string;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		return dbDelta( $query_string );
	}

}

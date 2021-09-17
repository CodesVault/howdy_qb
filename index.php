<?php
/**
 * Plugin Name:       WP Query Builder
 * Plugin URI:        https://wordpress.org/plugins/talash
 * Description:       Advanced Search plugin for WordPress. Next Level of WordPress search experience. <code>[talash-search]</code> use this shorCode to show the <strong>Talash Search Bar</strong>. You can customize the UI from customizer.
 * Version:           0.0.7
 * Author:            Keramot UL Islam 
 * Author URI:        https://abmsourav.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt 
 * Text Domain:       wp-qb
 * Domain Path:       /languages
 */

use WPQB\QueryBuilder\Add\Insert;
use WPQB\QueryBuilder\Get\Select;
use WPQB\QueryBuilder\Remove\Delete;
use WPQB\QueryBuilder\Renovate\Update;
use WPQB\QueryBuilder\WPQuery;

require plugin_dir_path( __FILE__ ) . "vendor/autoload.php";


// $query = WPQuery::select( function(Select $db) {
// 	return $db->column("post_title", true)->from('posts')
// 		->limit(4)
// 		->get();
// } );
// print_r($query);

$select = WPQuery::select( function(Select $db) {
	$db->column("posts.post_title", true)->from("posts as posts");
	$db->join("term_relationships as term_rel")->on("posts.ID = term_rel.object_id");
	$db->where("term_rel.term_taxonomy_id")->in(3, 6)->and("posts.post_status = %s");
	return $db->get([ 'publish' ]);
} );	
// print_r($select);

// $insert = WPQuery::insert( function(Insert $db) {
// 	$db->into("postmeta")
// 	->insertColumn("post_id, meta_key, meta_value")
// 	->select("options.option_id, options.option_name, options.option_value", true)
// 	->from("options as options")
// 		->where("options.option_name = %s")
// 	->add( ['start_of_week'] );
// } );
// print_r($insert);

// echo "<pre>";
// print_r(WPQuery::schema('postmeta'));
// echo "</pre>";

// $update = WPQuery::update( function(Update $db) {
// 	return $db->tableName('postmeta')
// 		->set("meta_value = %s")
// 		->where("meta_key = %s")
// 		->renovate([ 24, 'start_of_week' ]);
// } );
// print_r($update);

// $delete = WPQuery::delete( function(Delete $db) {
// 	return $db->from('postmeta as postmeta')
// 		->where("postmeta.meta_key = %s")
// 		->remove([ 'start_of_week' ]);
// } );
// print_r($delete);

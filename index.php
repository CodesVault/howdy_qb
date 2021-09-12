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
use WPQB\QueryBuilder\WPQuery;

require plugin_dir_path( __FILE__ ) . "vendor/autoload.php";

// $query = WPQuery::select("posts.post_title")
// 			->from("posts as posts")
// 			->limit(4)
// 			->get();
// print_r($query);

// $select = WPQuery::select("posts.post_type", true)
// 			->from("posts as posts")
// 			->join("term_relationships as term_rel")
// 				->on("posts.ID = term_rel.object_id")
// 			->where("term_rel.term_taxonomy_id")
// 				->in(6)
// 			->and("posts.post_status = %s")
// 			->get([ 'publish' ]);
// print_r($select);

// $insert = WPQuery::insert()
// 			->into("postmeta")
// 			->column("post_id, meta_key, meta_value")
// 			->select("options.option_id, options.option_name, options.option_value", true)
// 			->from("options as options")
// 				->where("options.option_name = %s")
// 			->add( ['start_of_week'] );
// print_r($insert);

// echo "<pre>";
// print_r(WPQuery::schema('postmeta'));
// echo "</pre>";

// $update = WPQuery::update("postmeta")
// 			->set("meta_value = %s")
// 			->where("meta_key = %s")
// 			->renovate([ 24, 'start_of_week' ]);
// print_r($update);

// $delete = WPQuery::delete()
// 			->from("postmeta as postmeta")
// 			->where("postmeta.meta_key = %s")
// 			->remove([ 'start_of_week' ]);
// print_r($delete);

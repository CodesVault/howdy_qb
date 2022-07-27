<?php
/**
 * @package wpq
 *
 * Plugin Name: WP Query Builder
 * Plugin URI: 
 * Description: A wordpress starter plugin.
 * Version: 0.0.1
 * Author: CodesVault
 * Author URI: https://github.com/CodesVault
 * License: GPLv2 or later
 * Text Domain: wpq
 */

if ( ! defined( 'ABSPATH' ) ) die();

// use CodesVault\WPqb\SqlEngine;

use CodesVault\WPqb\Connect;
use CodesVault\WPqb\DB;
// use CodesVault\WPqb\WPQueryBuilder;

// use function Latitude\QueryBuilder\field;

require_once __DIR__ . '/vendor/autoload.php';

// global $wpdb;
// $conn = Connect::run();
// $posts = $conn->prepare(DB::select('*')->from('posts')->get());
// $posts->execute();
// print_r($posts->fetchAll());

// $data = DB::select('ID', 'post_title')
//         ->distinct()
//         ->from('posts')
//         ->where(function($query) {
//             $query->where('post_type', '=', 'page')
//                 ->andWhere('post_status', '=', 'publish');
//         })
//         ->get();

$data = DB::select('posts.ID', 'posts.post_title')
        ->from('posts')
        ->alias('posts')
        // ->whereIn('ID', 12, 2, 5)
        ->orderBy(['post_date', 'post_title'], 'ASC')
        ->limit(4)
        ->get();

echo '<pre>';
print_r($data);
echo '</pre>';

// $host = $_SERVER['HTTP_HOST'];
// $dns =  'mysql:host=' . $host . ';dbname=' . $wpdb->dbname;
// $user = $wpdb->dbuser;
// $password = $wpdb->dbpassword;
// $connect = new \PDO($dns, $user, $password);

// $posts = $connect->prepare($data);
// $posts->execute(['page', 'publish']);

// print_r($posts->fetchAll());

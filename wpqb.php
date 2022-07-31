<?php
/**
 * @package wpqb
 *
 * @author CodesVault
 * @version 0.0.1
 */

use CodesVault\WPqb\Connect;
use CodesVault\WPqb\DB;

require_once __DIR__ . '/vendor/autoload.php';

// $data = DB::select('ID', 'post_title')
//         ->distinct()
//         ->from('posts')
//         ->where(function($query) {
//             $query->where('post_type', '=', 'page')
//                 ->andWhere('post_status', '=', 'publish');
//         })
//         ->get();

// $data = DB::select('posts.ID', 'posts.post_title')
//         ->from('posts')
//         ->alias('posts')
//         // ->whereIn('ID', 12, 2, 5)
//         ->orderBy(['post_date', 'post_title'], 'ASC')
//         ->limit(4)
//         ->get();

// echo '<pre>';
// print_r($data);
// echo '</pre>';


// $host = $_SERVER['HTTP_HOST'];
// $dns =  'mysql:host=' . $host . ';dbname=' . $wpdb->dbname;
// $user = $wpdb->dbuser;
// $password = $wpdb->dbpassword;
// $connect = new \PDO($dns, $user, $password);

// $posts = $connect->prepare($data);
// $posts->execute(['page', 'publish']);

// print_r($posts->fetchAll());

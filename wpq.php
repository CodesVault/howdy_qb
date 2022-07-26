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

use CodesVault\WPqb\SqlEngine;
use CodesVault\WPqb\WPQueryBuilder;

use function Latitude\QueryBuilder\field;

require_once __DIR__ . '/vendor/autoload.php';

global $wpdb;

$engine = new SqlEngine();
// $factory = new \Latitude\QueryBuilder\QueryFactory($engine);
$factory = new WPQueryBuilder($engine);
$posts = $factory
    ->select('post_title')
    ->from('posts')
    ->where(field('post_status')->eq('publish'));

$query = $posts->sql($engine);
$params = $posts->params($engine);
print_r($query);

$host = $_SERVER['HTTP_HOST'];
$dns =  'mysql:host=' . $host . ';dbname=' . $wpdb->dbname;
$user = $wpdb->dbuser;
$password = $wpdb->dbpassword;
$connect = new \PDO($dns, $user, $password);

$posts = $connect->prepare($query);
$posts->execute($params);

print_r($posts->fetchAll());

<?php
/**
 * Pest bootstrap file for WordPress testing
 */

// Load composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), '.env');
$dotenv->load();

// For integration tests that need real wpdb
if (isset($_ENV['TEST_INTEGRATION']) && $_ENV['TEST_INTEGRATION'] === 'true') {
    define('WP_USE_THEMES', false);
    define('SHORTINIT', true);

    ob_start();
    require_once '/var/www/html/wp-load.php';
    ob_end_clean();

	function wp_db() {
		global $wpdb;
		return $wpdb;
	}
}

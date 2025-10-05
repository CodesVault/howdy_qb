<?php
/**
 * Pest bootstrap file for WordPress testing
 */

// Load composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// TestCase will be imported per file as needed

// WordPress test config
define('WP_TESTS_CONFIG_PATH', __DIR__);

// Database settings for testing (should be different from main WordPress DB)
$_ENV['WP_TESTS_DB_NAME'] = 'wp_test';
$_ENV['WP_TESTS_DB_USER'] = 'wp';
$_ENV['WP_TESTS_DB_PASSWORD'] = 'secret';
$_ENV['WP_TESTS_DB_HOST'] = 'mysql';
$_ENV['WP_TESTS_TABLE_PREFIX'] = 'wptests_';

// WordPress constants
if (!defined('ABSPATH')) {
    define('ABSPATH', '/var/www/html/');
}

// Load WordPress test suite
// This will bootstrap the WordPress environment for testing
if (file_exists('/tmp/wordpress-tests-lib/includes/bootstrap.php')) {
    require_once '/tmp/wordpress-tests-lib/includes/bootstrap.php';
} else {
    // Try to load from WordPress installation
    if (file_exists(ABSPATH . 'wp-config.php')) {
        require_once ABSPATH . 'wp-config.php';
    }

    // Define WordPress constants if not already defined
    if (!defined('OBJECT')) {
        define('OBJECT', 'OBJECT');
    }
    if (!defined('ARRAY_A')) {
        define('ARRAY_A', 'ARRAY_A');
    }
}

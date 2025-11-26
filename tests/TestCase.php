<?php

declare(strict_types=1);

namespace CodesVault\Howdyqb\Tests;

use CodesVault\Howdyqb\DB;
use PHPUnit\Framework\TestCase as BaseTestCase;
use wpdb;

/**
 * Base Test Case for Howdy QB Tests
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Current driver being used for testing
     * @var string
     */
    protected $currentDriver = 'pdo';

    /**
     * Mock wpdb object for testing
     * @var wpdb|\PHPUnit\Framework\MockObject\MockObject|null
     */
    protected $mockWpdb = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Define WordPress constants if not already defined
        if (!defined('OBJECT')) {
            define('OBJECT', 'OBJECT');
        }
        if (!defined('ARRAY_A')) {
            define('ARRAY_A', 'ARRAY_A');
        }

        $this->setupMockWpdb();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Add any cleanup logic here
    }

    /**
     * Setup mock wpdb object for testing using PHPUnit's createMock
     */
    protected function setupMockWpdb(): void
    {
        // With WP-PHPUnit, we have access to the real WordPress environment
        // We can use the global $wpdb or create a mock when needed
        global $wpdb;

        if (!$wpdb || !($wpdb instanceof wpdb)) {
            // Create a PHPUnit mock for wpdb when the global is not available
            $this->mockWpdb = $this->createMock(wpdb::class);
			$db_config = $this->getDatabaseConfig();

            // Set up default properties
            $this->mockWpdb->prefix = $db_config['prefix'];
            $this->mockWpdb->dbhost = $db_config['dbhost'];
            $this->mockWpdb->dbname = $db_config['dbname'];
            $this->mockWpdb->dbuser = $db_config['dbuser'];
            $this->mockWpdb->dbpassword = $db_config['dbpassword'];
            $this->mockWpdb->last_error = '';
            $this->mockWpdb->insert_id = 0;
            $this->mockWpdb->rows_affected = 0;

            // Set up common method expectations
            $this->mockWpdb->method('set_prefix')
                ->willReturn('wp_');

            $this->mockWpdb->method('suppress_errors')
                ->willReturn(true);

            $this->mockWpdb->method('show_errors')
                ->willReturn(true);

            $this->mockWpdb->method('hide_errors')
                ->willReturn(true);

            $this->mockWpdb->method('flush')
                ->willReturn(true);

            $this->mockWpdb->method('db_connect')
                ->willReturn(true);

            $this->mockWpdb->method('_escape')
                ->willReturnCallback(function($data) {
                    if (is_array($data)) {
                        return array_map('addslashes', $data);
                    }
                    return is_string($data) ? addslashes($data) : $data;
                });

            $this->mockWpdb->method('prepare')
                ->willReturnCallback(function($query, ...$args) {
                    // Simple prepare implementation for testing
                    if (empty($args)) {
                        return $query;
                    }
                    $query = str_replace('%s', "'%s'", $query);
                    $query = str_replace('%d', '%d', $query);
                    return vsprintf($query, $args);
                });

            // Default successful responses for database operations
            $this->mockWpdb->method('query')
                ->willReturn(true);

            $this->mockWpdb->method('get_results')
                ->willReturn([]);

            $this->mockWpdb->method('get_row')
                ->willReturn(null);

            $this->mockWpdb->method('get_var')
                ->willReturn(null);

            // Set global wpdb for the query builder to use
            $wpdb = $this->mockWpdb;
        }
    }

    /**
     * Create a test database connection
     *
     * @return array Database configuration
     */
    public function getDatabaseConfig(): array
    {
        return [
            'dbhost' => 'mysql',
			'dbname' => 'wp',
			'dbuser' => 'wp',
			'dbpassword' => 'secret',
			'prefix' => 'wp_'
        ];
    }

    /**
     * Helper method to create a query builder instance for testing with PDO
     *
     * @return mixed
     */
    public function getQueryBuilder()
    {
        $this->currentDriver = 'pdo';

        return new DB('pdo');
    }

    /**
     * Helper method to create a query builder instance for testing with wpdb
     *
     * @return mixed
     */
    public function getQueryBuilderWithWpdb()
    {
        $this->currentDriver = 'wpdb';

        // Ensure mock wpdb is available globally
        global $wpdb;
        if (!$wpdb) {
            $wpdb = $this->mockWpdb;
        }

        return new DB('wpdb');
    }

    /**
     * Get query builder with specified driver
     *
     * @param string $driver Either 'pdo' or 'wpdb'
     * @return mixed
     */
    public function getQueryBuilderWithDriver(string $driver)
    {
        if ($driver === 'wpdb') {
            return $this->getQueryBuilderWithWpdb();
        }

        return $this->getQueryBuilder();
    }

    /**
     * Run a test with both PDO and wpdb drivers
     *
     * @param callable $testCallback
     * @return void
     */
    public function runWithBothDrivers(callable $testCallback): void
    {
        // Test with PDO
        $this->currentDriver = 'pdo';
        $pdoDb = $this->getQueryBuilder();
        $testCallback($pdoDb, 'pdo');

        // Test with wpdb
        $this->currentDriver = 'wpdb';
        $wpdbDb = $this->getQueryBuilderWithWpdb();
        $testCallback($wpdbDb, 'wpdb');
    }

    /**
     * Get the current driver being used
     *
     * @return string
     */
    public function getCurrentDriver(): string
    {
        return $this->currentDriver;
    }
}

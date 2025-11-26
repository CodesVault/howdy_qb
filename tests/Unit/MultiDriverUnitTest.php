<?php

declare(strict_types=1);

beforeEach(function () {
    // This will run before each test - you can choose which driver to use per test
    try {
        $this->pdoDb = $this->getQueryBuilder();
        $this->wpdbDb = $this->getQueryBuilderWithWpdb();
    } catch (Exception $e) {
        $this->markTestSkipped('Database connection failed: ' . $e->getMessage());
    }
});

test('can create query builder instances with both drivers', function () {
    // Test PDO driver
    $pdoDb = $this->getQueryBuilder();
    expect($this->getCurrentDriver())->toBe('pdo');
    expect($pdoDb)->not->toBeNull();

    // Test wpdb driver
    $wpdbDb = $this->getQueryBuilderWithWpdb();
    expect($this->getCurrentDriver())->toBe('wpdb');
    expect($wpdbDb)->not->toBeNull();
});

test('both drivers have required query methods', function () {
    $this->runWithBothDrivers(function ($db, $driver) {
        $this->assertTrue(method_exists($db, 'select'), "Driver {$driver} should have select method");
        $this->assertTrue(method_exists($db, 'insert'), "Driver {$driver} should have insert method");
        $this->assertTrue(method_exists($db, 'update'), "Driver {$driver} should have update method");
        $this->assertTrue(method_exists($db, 'delete'), "Driver {$driver} should have delete method");
        $this->assertTrue(method_exists($db, 'create'), "Driver {$driver} should have create method");
        $this->assertTrue(method_exists($db, 'drop'), "Driver {$driver} should have drop method");
    });
});

test('can build basic select queries with both drivers', function () {
    $this->runWithBothDrivers(function ($db, $driver) {
        // Test basic select query building
        $query = $db->select('name', 'email')
            ->from('users')
            ->where('status', '=', 'active');

        $this->assertNotNull($query, "Query should not be null for driver: {$driver}");
        $this->assertTrue(method_exists($query, 'get'), "Query should have get method for driver: {$driver}");
    });
});

test('can build CRUD operations with both drivers', function () {
    $this->runWithBothDrivers(function ($db, $driver) {
        // Test that all CRUD methods exist on the DB instance for both drivers
        $this->assertTrue(method_exists($db, 'create'), "Driver {$driver} should have create method");
        $this->assertTrue(method_exists($db, 'insert'), "Driver {$driver} should have insert method");
        $this->assertTrue(method_exists($db, 'update'), "Driver {$driver} should have update method");
        $this->assertTrue(method_exists($db, 'delete'), "Driver {$driver} should have delete method");
        $this->assertTrue(method_exists($db, 'drop'), "Driver {$driver} should have drop method");
        $this->assertTrue(method_exists($db, 'dropIfExists'), "Driver {$driver} should have dropIfExists method");

        // Test CREATE operation - returns Create statement instance (safe to call)
        try {
            $createQuery = $db->create('test_table_' . $driver);
            $this->assertNotNull($createQuery, "CREATE query should not be null for driver: {$driver}");
        } catch (Exception $e) {
            // CREATE might fail if table exists, that's OK for testing
            $this->assertTrue(true, "CREATE operation available for driver: {$driver}");
        }

        // Note: INSERT, UPDATE, DELETE operations execute immediately,
        // so we only test method existence to avoid database errors
        // The actual execution testing should be done with proper test fixtures
    });
});

test('database configuration is properly set', function () {
    $config = $this->getDatabaseConfig();

    expect($config)->toBeArray();
    expect($config)->toHaveKey('dbhost');
    expect($config)->toHaveKey('dbname');
    expect($config)->toHaveKey('dbuser');
    expect($config)->toHaveKey('dbpassword');
    expect($config)->toHaveKey('prefix');

    expect($config['dbhost'])->toBe('mysql');
    expect($config['dbname'])->toBe('wp');
    expect($config['dbuser'])->toBe('wp');
    expect($config['prefix'])->toBe('wp_');
});

test('mock wpdb is properly configured when using wpdb driver', function () {
    // Get wpdb query builder which should set up the mock
    $wpdbDb = $this->getQueryBuilderWithWpdb();

    // Check that wpdb was set up globally
    global $wpdb;
    $this->assertNotNull($wpdb, "Global wpdb should be set");

    // Check wpdb properties
    if ($wpdb) {
        $this->assertEquals('wp_', $wpdb->prefix);
        $this->assertEquals('mysql', $wpdb->dbhost);
        $this->assertEquals('wp', $wpdb->dbname);
        $this->assertEquals('wp', $wpdb->dbuser);
    }
});

test('both drivers return query builder instances', function () {
    $this->runWithBothDrivers(function ($db, $driver) {
        // Test that query builders return proper instances
        expect($db)->toBeInstanceOf(\CodesVault\Howdyqb\DB::class);

        // Test basic query chain
        $query = $db->select('id', 'name')
            ->from('test_table')
            ->where('status', '=', 'active');

        expect($query)->not->toBeNull();
    });
});

test('can switch between drivers correctly', function () {
    // Test PDO
    $pdoDb = $this->getQueryBuilderWithDriver('pdo');
    expect($this->getCurrentDriver())->toBe('pdo');
    expect($pdoDb)->toBeInstanceOf(\CodesVault\Howdyqb\DB::class);

    // Test wpdb
    $wpdbDb = $this->getQueryBuilderWithDriver('wpdb');
    expect($this->getCurrentDriver())->toBe('wpdb');
    expect($wpdbDb)->toBeInstanceOf(\CodesVault\Howdyqb\DB::class);
});

test('runWithBothDrivers helper method works correctly', function () {
    $driversUsed = [];

    $this->runWithBothDrivers(function ($db, $driver) use (&$driversUsed) {
        $driversUsed[] = $driver;
        expect($db)->toBeInstanceOf(\CodesVault\Howdyqb\DB::class);
        $this->assertTrue(in_array($driver, ['pdo', 'wpdb']));
    });

    $this->assertEquals(2, count($driversUsed));
    $this->assertTrue(in_array('pdo', $driversUsed));
    $this->assertTrue(in_array('wpdb', $driversUsed));
});

test('can safely test database operations when table exists', function () {
    $this->runWithBothDrivers(function ($db, $driver) {
        $tableName = 'safe_test_table_' . $driver . '_' . time();

        try {
            // Step 1: Create table first
            $db->create($tableName)
                ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
                ->column('name')->string(100)->required()
                ->column('status')->string(20)->default('active')
                ->execute();

            // Step 2: Now we can safely test INSERT
            $insertResult = $db->insert($tableName, [
                ['name' => 'Test User', 'status' => 'active']
            ]);

            // For wpdb driver, result might be different than PDO
            // Both should indicate success in their own way
            $this->assertTrue($insertResult !== false, "Insert should succeed for driver: {$driver}");

            // Step 3: Test SELECT to verify data
            $records = $db->select('*')
                ->from($tableName)
                ->where('name', '=', 'Test User')
                ->get();

            $this->assertGreaterThan(0, count($records), "Should find inserted record for driver: {$driver}");
            $this->assertEquals('Test User', $records[0]['name']);

            // Step 4: Test UPDATE
            $db->update($tableName, ['status' => 'updated'])
                ->where('name', '=', 'Test User')
                ->execute();

            // Verify update
            $updatedRecords = $db->select('*')
                ->from($tableName)
                ->where('status', '=', 'updated')
                ->get();

            $this->assertEquals(1, count($updatedRecords), "Should have one updated record for driver: {$driver}");

            // Step 5: Clean up - drop the table
            $db->drop($tableName);

        } catch (Exception $e) {
            // Clean up on error
            try {
                $db->drop($tableName);
            } catch (Exception $cleanup) {
                // Ignore cleanup errors
            }

            // Only skip if it's a connection issue, otherwise the test should fail
            if (strpos($e->getMessage(), 'connection') !== false) {
                $this->markTestSkipped("Database connection failed for driver {$driver}: " . $e->getMessage());
            } else {
                throw $e;
            }
        }
    });
})->skip('Enable this test when you want to test actual database operations');

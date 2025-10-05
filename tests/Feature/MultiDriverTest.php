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

test('can insert and select with PDO driver', function () {
    $db = $this->pdoDb;

    // Create table
    $db->create('pdo_test')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->execute();

    // Insert data
    $result = $db->insert('pdo_test', [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]
    ]);

    $this->assertTrue(is_object($result) || $result === true);

    // Select data
    $records = $db->select('*')
        ->from('pdo_test')
        ->where('email', '=', 'john@example.com')
        ->get();

    $this->assertGreaterThan(0, count($records));
    $this->assertEquals('John Doe', $records[0]['name']);
});

test('can insert and select with wpdb driver', function () {
    $db = $this->wpdbDb;

    // Create table
    $db->create('wpdb_test')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->execute();

    // Insert data
    $result = $db->insert('wpdb_test', [
        [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com'
        ]
    ]);

    $this->assertTrue(is_object($result) || $result === true);

    // Select data
    $records = $db->select('*')
        ->from('wpdb_test')
        ->where('email', '=', 'jane@example.com')
        ->get();

    $this->assertGreaterThan(0, count($records));
    $this->assertEquals('Jane Doe', $records[0]['name']);
});

test('can test same functionality with both drivers using helper method', function () {
    $this->runWithBothDrivers(function ($db, $driver) {
        // Create a table specific to this test and driver
        $tableName = 'multi_driver_test_' . $driver;

        $db->create($tableName)
            ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
            ->column('title')->string(200)->required()
            ->column('status')->string(20)->default('active')
            ->execute();

        // Insert test data
        $result = $db->insert($tableName, [
            [
                'title' => "Test post for {$driver}",
                'status' => 'published'
            ],
            [
                'title' => "Another post for {$driver}",
                'status' => 'draft'
            ]
        ]);

        $this->assertTrue(is_object($result) || $result === true);

        // Test SELECT with WHERE clause
        $publishedPosts = $db->select('*')
            ->from($tableName)
            ->where('status', '=', 'published')
            ->get();

        $this->assertEquals(1, count($publishedPosts));
        $this->assertEquals("Test post for {$driver}", $publishedPosts[0]['title']);

        // Test COUNT
        $totalCount = $db->select()
            ->count('*', 'total')
            ->from($tableName)
            ->get();

        $this->assertEquals(2, (int)$totalCount[0]['total']);

        // Test UPDATE
        $updateResult = $db->update($tableName, ['status' => 'archived'])
            ->where('status', '=', 'draft')
            ->execute();

        // Verify update
        $archivedPosts = $db->select('*')
            ->from($tableName)
            ->where('status', '=', 'archived')
            ->get();

        $this->assertEquals(1, count($archivedPosts));
        $this->assertEquals("Another post for {$driver}", $archivedPosts[0]['title']);
    });
});

test('can test driver-specific behavior', function () {
    // Test PDO specific behavior
    $pdoDb = $this->getQueryBuilder();
    $this->assertEquals('pdo', $this->getCurrentDriver());

    // Test wpdb specific behavior
    $wpdbDb = $this->getQueryBuilderWithWpdb();
    $this->assertEquals('wpdb', $this->getCurrentDriver());

    // You can add driver-specific tests here
    // For example, testing different SQL generation or error handling
});

afterEach(function () {
    // Clean up test tables for both drivers
    $tablesToClean = ['pdo_test', 'wpdb_test', 'multi_driver_test_pdo', 'multi_driver_test_wpdb'];

    foreach ($tablesToClean as $table) {
        try {
            if (isset($this->pdoDb)) {
                $this->pdoDb->drop($table)->execute();
            }
        } catch (Exception $e) {
            // Table might not exist, ignore cleanup errors
        }

        try {
            if (isset($this->wpdbDb)) {
                $this->wpdbDb->drop($table)->execute();
            }
        } catch (Exception $e) {
            // Table might not exist, ignore cleanup errors
        }
    }
});

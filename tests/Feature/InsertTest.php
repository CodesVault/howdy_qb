<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
	$this->db = $this->getQueryBuilder();

    // Create the querybuilder table if it doesn't exist
    $this->db->create('querybuilder')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()
        ->column('country')->string(50)
        ->execute();
});

afterEach(function () {
    // Clean up test data after each test
    try {
        $this->db->delete('querybuilder')->execute();
    } catch (Exception $e) {
        // Handle cleanup errors gracefully
    }
});

test('select method chains correctly', function () {
    $select = $this->db->select('*')
        ->from('qb_posts');

    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Select::class, $select);
});

test('can insert single record into querybuilder table', function () {
    // Test data for single record insertion
    $testData = [
        [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'age' => 30,
            'country' => 'USA'
        ]
    ];

    // Insert the data
    $result = $this->db->insert('querybuilder', $testData);

    // Verify the insertion returns something (indicating success)
    $this->assertTrue(is_object($result) || $result === true);

    // Verify the data was inserted correctly
    $insertedRecord = $this->db->select('id', 'name', 'email', 'age', 'country')
        ->from('querybuilder')
        ->where('email', '=', 'john.doe@example.com')
        ->get();

    $this->assertGreaterThan(0, count($insertedRecord));
    $this->assertEquals('John Doe', $insertedRecord[0]['name']);
    $this->assertEquals('john.doe@example.com', $insertedRecord[0]['email']);
    $this->assertEquals(30, (int)$insertedRecord[0]['age']);
    $this->assertEquals('USA', $insertedRecord[0]['country']);
});

test('can insert multiple records into querybuilder table', function () {
    // Test data for multiple records insertion
    $testData = [
        [
            'name' => 'Alice Smith',
            'email' => 'alice.smith@example.com',
            'age' => 25,
            'country' => 'Canada'
        ],
        [
            'name' => 'Bob Johnson',
            'email' => 'bob.johnson@example.com',
            'age' => 35,
            'country' => 'UK'
        ],
        [
            'name' => 'Carol Williams',
            'email' => 'carol.williams@example.com',
            'age' => 28,
            'country' => 'Australia'
        ]
    ];

    // Insert the data
    $result = $this->db->insert('querybuilder', $testData);

    // Verify the insertion returns something (indicating success)
    $this->assertTrue(is_object($result) || $result === true);

    // Verify all records were inserted correctly
    $insertedRecords = $this->db->select('name', 'email', 'age', 'country')
        ->from('querybuilder')
        ->get();

    $this->assertGreaterThanOrEqual(3, count($insertedRecords));

    // Check that specific emails exist
    $emails = array_column($insertedRecords, 'email');
    $this->assertContains('alice.smith@example.com', $emails);
    $this->assertContains('bob.johnson@example.com', $emails);
    $this->assertContains('carol.williams@example.com', $emails);
});

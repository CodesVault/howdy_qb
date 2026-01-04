<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
	$this->db = $this->getQueryBuilder();

    // Create the querybuilder table (target table for inserts)
    $this->db->create('querybuilder')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()
        ->column('country')->string(50)
        ->execute();

    // Create the qb_source table (source table for INSERT...SELECT tests)
    $this->db->create('qb_source')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()
        ->column('country')->string(50)
        ->execute();

    // Create the qb_countries table (for subquery filter tests)
    $this->db->create('qb_countries')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('country')->string(50)->required()
        ->execute();
});

afterEach(function () {
    // Clean up test data after each test
    try {
        $this->db->delete('querybuilder')->execute();
        $this->db->delete('qb_source')->execute();
        $this->db->delete('qb_countries')->execute();
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
    $result = $this->db->insert('querybuilder', $testData)->execute();

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
    $result = $this->db->insert('querybuilder', $testData)->execute();

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

test('can insert with select from another table', function () {
    // Insert data into qb_source table
    $this->db->insert('qb_source', [
        ['name' => 'Source User 1', 'email' => 'source1@example.com', 'age' => 25, 'country' => 'USA'],
        ['name' => 'Source User 2', 'email' => 'source2@example.com', 'age' => 30, 'country' => 'Canada']
    ])->execute();

    // Insert into querybuilder from qb_source with a WHERE condition
    $this->db->insert('querybuilder', ['name', 'email', 'age', 'country'])
        ->select('name', 'email', 'age', 'country')
        ->from('qb_source')
        ->where('country', '=', 'USA')
        ->execute();

    // Verify only the USA record was inserted
    $results = $this->db->select('*')
        ->from('querybuilder')
        ->get();

    $this->assertEquals(1, count($results));
    $this->assertEquals('Source User 1', $results[0]['name']);
    $this->assertEquals('USA', $results[0]['country']);
});

test('can insert with select using subquery in where clause', function () {
    // Insert test data into qb_source
    $this->db->insert('qb_source', [
        ['name' => 'User A', 'email' => 'a@example.com', 'age' => 20, 'country' => 'USA'],
        ['name' => 'User B', 'email' => 'b@example.com', 'age' => 25, 'country' => 'Canada'],
        ['name' => 'User C', 'email' => 'c@example.com', 'age' => 30, 'country' => 'UK']
    ])->execute();

    // Insert allowed countries into qb_countries
    $this->db->insert('qb_countries', [
        ['country' => 'USA'],
        ['country' => 'UK']
    ])->execute();

    // Insert into querybuilder using subquery to filter by allowed countries
    $this->db->insert('querybuilder', ['name', 'email', 'age', 'country'])
        ->select('name', 'email', 'age', 'country')
        ->from('qb_source')
        ->whereIn('country', function ($subQuery) {
            $subQuery->select('country')
                ->from('qb_countries');
        })
        ->execute();

    // Verify only users from allowed countries were inserted
    $results = $this->db->select('*')
        ->from('querybuilder')
        ->orderBy('name', 'ASC')
        ->get();

    $this->assertEquals(2, count($results));
    $countries = array_column($results, 'country');
    $this->assertContains('USA', $countries);
    $this->assertContains('UK', $countries);
    $this->assertNotContains('Canada', $countries);
});

test('can insert ignore duplicates with select and multiple conditions', function () {
    // Insert test data into qb_source
    $this->db->insert('qb_source', [
        ['name' => 'Young Employee', 'email' => 'young@example.com', 'age' => 22, 'country' => 'USA'],
        ['name' => 'Senior Employee', 'email' => 'senior@example.com', 'age' => 45, 'country' => 'USA'],
        ['name' => 'Mid Employee', 'email' => 'mid@example.com', 'age' => 30, 'country' => 'Canada']
    ])->execute();

    // Insert with ignoreDuplicates, selecting employees from USA aged 25+
    $this->db->insert('querybuilder', ['name', 'email', 'age', 'country'])
        ->ignoreDuplicates()
        ->select('name', 'email', 'age', 'country')
        ->from('qb_source')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 25)
        ->execute();

    // Verify only the matching record was inserted
    $results = $this->db->select('*')
        ->from('querybuilder')
        ->get();

    $this->assertEquals(1, count($results));
    $this->assertEquals('Senior Employee', $results[0]['name']);
    $this->assertEquals(45, (int)$results[0]['age']);
});

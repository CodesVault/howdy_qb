<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
    $this->db = $this->getQueryBuilder();

    // Create the test_users table for testing
    $this->db->create('test_users')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(250)->required()
        ->column('age')->int()
        ->column('country')->string(50)->nullable()
        ->column('status')->string(50)->default('active')
        ->execute();

    // Insert sample data for testing
    $this->db->insert('test_users', [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30,
            'country' => 'USA',
            'status' => 'active'
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'age' => 25,
            'country' => 'Canada',
            'status' => 'active'
        ],
        [
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'age' => 35,
            'country' => 'UK',
            'status' => 'inactive'
        ],
        [
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'age' => 28,
            'country' => 'USA',
            'status' => 'active'
        ]
    ]);
});

afterEach(function () {
    // Clean up test tables after each test
    try {
        $this->db->dropIfExists('test_users');
    } catch (Exception $e) {
        // Table might not exist, ignore
    }
});

test('can update single column with where clause', function () {
    // Update a single user's name
    $this->db->update('test_users', ['name' => 'John Updated'])
        ->where('email', '=', 'john@example.com')
        ->execute();

    // Verify the update
    $result = $this->db->select('*')
        ->from('test_users')
        ->where('email', '=', 'john@example.com')
        ->get();

    $this->assertEquals('John Updated', $result[0]['name']);
});

test('can update multiple columns with where clause', function () {
    // Update multiple columns
    $this->db->update('test_users', [
        'name'		=> 'Jane Updated',
        'age'		=> 26,
        'country'	=> 'Mexico'
    ])
	->where('email', '=', 'jane@example.com')
	->execute();

    // Verify the update
    $result = $this->db->select('name', 'age', 'country')
        ->from('test_users')
        ->where('email', '=', 'jane@example.com')
        ->get();

    $this->assertEquals('Jane Updated', $result[0]['name']);
    $this->assertEquals(26, (int)$result[0]['age']);
    $this->assertEquals('Mexico', $result[0]['country']);
});

test('can update with andWhere clause', function () {
    // Update users with specific conditions
    $this->db->update('test_users', ['status' => 'premium'])
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 30)
        ->execute();

    // Verify the update
    $result = $this->db->select('name', 'status')
        ->from('test_users')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 30)
        ->get();

    foreach ($result as $row) {
        $this->assertEquals('premium', $row['status']);
    }
});

test('can update with orWhere clause', function () {
    // Update users from USA or Canada
    $this->db->update('test_users', ['status' => 'verified'])
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->execute();

    // Verify the update
    $usaUsers = $this->db->select('status')
        ->from('test_users')
        ->where('country', '=', 'USA')
        ->get();

    $canadaUsers = $this->db->select('status')
        ->from('test_users')
        ->where('country', '=', 'Canada')
        ->get();

    foreach ($usaUsers as $user) {
        $this->assertEquals('verified', $user['status']);
    }
    foreach ($canadaUsers as $user) {
        $this->assertEquals('verified', $user['status']);
    }
});

test('can update with multiple andWhere clauses', function () {
    // Update with multiple AND conditions
    $this->db->update('test_users', ['name' => 'Special User'])
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->andWhere('status', '=', 'active')
        ->execute();

    // Verify the update
    $result = $this->db->select('name')
        ->from('test_users')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->get();

    $this->assertGreaterThan(0, count($result));
});

test('can update with multiple orWhere clauses', function () {
    // Update users from USA, Canada or UK
    $this->db->update('test_users', ['status' => 'international'])
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->orWhere('country', '=', 'UK')
        ->execute();

    // Verify all users are updated
    $result = $this->db->select('status')
        ->from('test_users')
        ->get();

    foreach ($result as $user) {
        $this->assertEquals('international', $user['status']);
    }
});

test('can update with mixed andWhere and orWhere clauses', function () {
    // Update with mixed conditions
    $this->db->update('test_users', ['name' => 'Mixed Update'])
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 28)
        ->orWhere('country', '=', 'UK')
        ->execute();

    // Verify the update
    $result = $this->db->select('name', 'country')
        ->from('test_users')
        ->where('country', '=', 'UK')
        ->get();

    $this->assertGreaterThan(0, count($result));
});

test('getSql returns correct update query structure', function () {
    $sql = $this->db->update('test_users', ['name' => 'Test User'])
        ->where('id', '=', 1)
        ->getSql();

    $this->assertIsArray($sql);
    $this->assertArrayHasKey('query', $sql);
    $this->assertArrayHasKey('params', $sql);
    $this->assertStringContainsString('UPDATE', $sql['query']);
    $this->assertStringContainsString('SET', $sql['query']);
    $this->assertStringContainsString('WHERE', $sql['query']);
});

test('getSql returns correct params array', function () {
    $sql = $this->db->update('test_users', [
        'name' => 'Test User',
        'age' => 30
    ])
        ->where('email', '=', 'test@example.com')
        ->getSql();

    $this->assertIsArray($sql['params']);
    $this->assertCount(3, $sql['params']); // 2 for SET values + 1 for WHERE value
    $this->assertEquals('Test User', $sql['params'][0]);
    $this->assertEquals(30, $sql['params'][1]);
    $this->assertEquals('test@example.com', $sql['params'][2]);
});

test('getSql with andWhere returns correct params', function () {
    $sql = $this->db->update('test_users', ['name' => 'Updated'])
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->getSql();

    $this->assertIsArray($sql['params']);
    $this->assertCount(3, $sql['params']); // 1 for SET + 2 for WHERE conditions
});

test('getSql with orWhere returns correct params', function () {
    $sql = $this->db->update('test_users', ['status' => 'updated'])
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->getSql();

    $this->assertIsArray($sql['params']);
    $this->assertCount(3, $sql['params']); // 1 for SET + 2 for WHERE conditions
});

test('can update with integer value', function () {
    $this->db->update('test_users', ['age' => 40])
        ->where('email', '=', 'bob@example.com')
        ->execute();

    $result = $this->db->select('age')
        ->from('test_users')
        ->where('email', '=', 'bob@example.com')
        ->get();

    $this->assertEquals(40, (int)$result[0]['age']);
});

test('can update with string value', function () {
    $this->db->update('test_users', ['country' => 'Australia'])
        ->where('email', '=', 'alice@example.com')
        ->execute();

    $result = $this->db->select('country')
        ->from('test_users')
        ->where('email', '=', 'alice@example.com')
        ->get();

    $this->assertEquals('Australia', $result[0]['country']);
});

test('can update with null value', function () {
    $this->db->update('test_users', ['age' => null])
        ->where('email', '=', 'john@example.com')
        ->execute();

    $result = $this->db->select('age')
        ->from('test_users')
        ->where('email', '=', 'john@example.com')
        ->get();

    $this->assertNull($result[0]['age']);
});

test('can update multiple records at once', function () {
    // Update all active users
    $this->db->update('test_users', ['status' => 'verified'])
        ->where('status', '=', 'active')
        ->execute();

    // Verify all active users are now verified
    $result = $this->db->select('status')
        ->from('test_users')
        ->where('status', '=', 'verified')
        ->get();

    $this->assertGreaterThanOrEqual(3, count($result)); // We had 3 active users
});

test('update method chains correctly', function () {
    $update = $this->db->update('test_users', ['name' => 'Test']);

    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Update::class, $update);

    $update = $update->where('id', '=', 1);
    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Update::class, $update);
});

test('can update with less than operator', function () {
    $this->db->update('test_users', ['status' => 'young'])
        ->where('age', '<', 30)
        ->execute();

    $result = $this->db->select('name', 'status')
        ->from('test_users')
        ->where('age', '<', 30)
        ->get();

    foreach ($result as $row) {
        $this->assertEquals('young', $row['status']);
    }
});

test('can update with greater than or equal operator', function () {
    $this->db->update('test_users', ['status' => 'senior'])
        ->where('age', '>=', 35)
        ->execute();

    $result = $this->db->select('name', 'status')
        ->from('test_users')
        ->where('age', '>=', 35)
        ->get();

    foreach ($result as $row) {
        $this->assertEquals('senior', $row['status']);
    }
});

test('can update with not equal operator', function () {
    $this->db->update('test_users', ['status' => 'non-usa'])
        ->where('country', '!=', 'USA')
        ->execute();

    $result = $this->db->select('country', 'status')
        ->from('test_users')
        ->where('country', '!=', 'USA')
        ->get();

    foreach ($result as $row) {
        $this->assertEquals('non-usa', $row['status']);
    }
});

test('update affects correct number of records', function () {
    // Update specific user
    $this->db->update('test_users', ['name' => 'Single Update'])
        ->where('email', '=', 'john@example.com')
        ->execute();

    // Verify only one record was updated
    $result = $this->db->select('name')
        ->from('test_users')
        ->where('name', '=', 'Single Update')
        ->get();

    $this->assertCount(1, $result);
});

test('can update empty data does not throw error', function () {
    $sql = $this->db->update('test_users', [])
        ->where('id', '=', 1)
        ->getSql();

    $this->assertIsArray($sql);
    $this->assertArrayHasKey('query', $sql);
});

test('update preserves other column values', function () {
    // Get original values
    $original = $this->db->select('name', 'email', 'age', 'country')
        ->from('test_users')
        ->where('email', '=', 'jane@example.com')
        ->get();

    // Update only name
    $this->db->update('test_users', ['name' => 'Jane Modified'])
        ->where('email', '=', 'jane@example.com')
        ->execute();

    // Verify other columns are unchanged
    $updated = $this->db->select('name', 'email', 'age', 'country')
        ->from('test_users')
        ->where('email', '=', 'jane@example.com')
        ->get();

    $this->assertEquals('Jane Modified', $updated[0]['name']);
    $this->assertEquals($original[0]['email'], $updated[0]['email']);
    $this->assertEquals($original[0]['age'], $updated[0]['age']);
    $this->assertEquals($original[0]['country'], $updated[0]['country']);
});

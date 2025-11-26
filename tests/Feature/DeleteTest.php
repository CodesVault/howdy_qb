<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
    $this->db = $this->getQueryBuilder();

    // Create a base table for delete testing
    $this->db->create('test_delete')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()
        ->column('country')->string(50)->nullable()
        ->column('status')->string(50)->default('active')
        ->execute();

    // Insert sample data for testing
    $this->db->insert('test_delete', [
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
        $this->db->dropIfExists('test_delete');
    } catch (Exception $e) {
        // Table might not exist, ignore
    }
});

test('delete method chains correctly', function () {
    $delete = $this->db->delete('test_delete');

    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Delete::class, $delete);

    $delete = $delete->where('id', '=', 1);
    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Delete::class, $delete);
});

test('getSql returns correct delete query structure', function () {
    $sql = $this->db->delete('test_delete')
        ->where('id', '=', 1)
        ->getSql();

    $this->assertIsArray($sql);
    $this->assertArrayHasKey('query', $sql);
    $this->assertArrayHasKey('params', $sql);
    $this->assertStringContainsString('DELETE FROM', $sql['query']);
    $this->assertStringContainsString('WHERE', $sql['query']);
});

test('can delete single record with where clause', function () {
    // Delete a specific user
    $this->db->delete('test_delete')
        ->where('email', '=', 'john@example.com')
        ->execute();

    // Verify the record was deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->where('email', '=', 'john@example.com')
        ->get();

    $this->assertCount(0, $result);

    // Verify other records still exist
    $allResults = $this->db->select('*')
        ->from('test_delete')
        ->get();

    $this->assertCount(3, $allResults);
});

test('can delete with andWhere clause', function () {
    // Delete users with specific conditions
    $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 30)
        ->execute();

    // Verify the correct record was deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 30)
        ->get();

    $this->assertCount(0, $result);

    // Verify other USA users with age < 30 still exist
    $usaUsers = $this->db->select('*')
        ->from('test_delete')
        ->where('country', '=', 'USA')
        ->get();

    $this->assertCount(1, $usaUsers);
});

test('can delete with orWhere clause', function () {
    // Delete users from USA or Canada
    $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->execute();

    // Verify USA and Canada users were deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should only have UK user left
    $this->assertCount(1, $result);
    $this->assertEquals('UK', $result[0]['country']);
});

test('can delete with multiple andWhere clauses', function () {
    // Delete with multiple AND conditions
    // This will match: country=USA AND age>25 AND status=active
    // From sample data: John (USA, 30, active) and Alice (USA, 28, active)
    // Both match country=USA AND status=active, and both have age>25
    $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->andWhere('status', '=', 'active')
        ->execute();

    // Verify the correct records were deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->get();

    $this->assertCount(0, $result);

    // Verify other records still exist (Jane and Bob)
    $allResults = $this->db->select('*')
        ->from('test_delete')
        ->get();

    $this->assertCount(2, $allResults);
});

test('can delete with multiple orWhere clauses', function () {
    // Delete users from USA, Canada or UK
    $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->orWhere('country', '=', 'UK')
        ->execute();

    // Verify all users are deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    $this->assertCount(0, $result);
});

test('can delete with mixed andWhere and orWhere clauses', function () {
    // Delete with mixed conditions
    $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>=', 28)
        ->orWhere('country', '=', 'UK')
        ->execute();

    // Verify the correct records were deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should only have Canada user left
    $this->assertCount(1, $result);
    $this->assertEquals('Canada', $result[0]['country']);
});

test('can delete with less than operator', function () {
    $this->db->delete('test_delete')
        ->where('age', '<', 30)
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should have 2 records left (age >= 30)
    $this->assertCount(2, $result);

    foreach ($result as $row) {
        $this->assertGreaterThanOrEqual(30, (int)$row['age']);
    }
});

test('can delete with greater than or equal operator', function () {
    $this->db->delete('test_delete')
        ->where('age', '>=', 35)
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should have 3 records left (age < 35)
    $this->assertCount(3, $result);

    foreach ($result as $row) {
        $this->assertLessThan(35, (int)$row['age']);
    }
});

test('can delete with not equal operator', function () {
    $this->db->delete('test_delete')
        ->where('country', '!=', 'USA')
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should only have USA users left
    $this->assertCount(2, $result);

    foreach ($result as $row) {
        $this->assertEquals('USA', $row['country']);
    }
});

test('can delete with greater than operator', function () {
    $this->db->delete('test_delete')
        ->where('age', '>', 30)
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should have 3 records left (age <= 30)
    $this->assertCount(3, $result);
});

test('can delete with less than or equal operator', function () {
    $this->db->delete('test_delete')
        ->where('age', '<=', 28)
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should have 2 records left (age > 28)
    $this->assertCount(2, $result);

    foreach ($result as $row) {
        $this->assertGreaterThan(28, (int)$row['age']);
    }
});

test('getSql returns correct params array', function () {
    $sql = $this->db->delete('test_delete')
        ->where('email', '=', 'test@example.com')
        ->getSql();

    $this->assertIsArray($sql['params']);
    $this->assertCount(1, $sql['params']);
    $this->assertEquals('test@example.com', $sql['params'][0]);
});

test('getSql with andWhere returns correct params', function () {
    $sql = $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->getSql();

    $this->assertIsArray($sql['params']);
    $this->assertCount(2, $sql['params']);
});

test('getSql with orWhere returns correct params', function () {
    $sql = $this->db->delete('test_delete')
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->getSql();

    $this->assertIsArray($sql['params']);
    $this->assertCount(2, $sql['params']);
});

test('can delete multiple records at once', function () {
    // Delete all active users
    $this->db->delete('test_delete')
        ->where('status', '=', 'active')
        ->execute();

    // Verify active users are deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    // Should only have 1 inactive user left
    $this->assertCount(1, $result);
    $this->assertEquals('inactive', $result[0]['status']);
});

test('delete affects correct number of records', function () {
    // Delete specific user
    $this->db->delete('test_delete')
        ->where('email', '=', 'john@example.com')
        ->execute();

    // Verify only one record was deleted
    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    $this->assertCount(3, $result);

    // Verify the correct user was deleted
    foreach ($result as $row) {
        $this->assertNotEquals('john@example.com', $row['email']);
    }
});

test('delete with no matching records does not affect table', function () {
    // Try to delete non-existent user
    $this->db->delete('test_delete')
        ->where('email', '=', 'nonexistent@example.com')
        ->execute();

    // Verify all records still exist
    $result = $this->db->select('*')
        ->from('test_delete')
        ->get();

    $this->assertCount(4, $result);
});

test('can delete with string comparison', function () {
    $this->db->delete('test_delete')
        ->where('name', '=', 'John Doe')
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->where('name', '=', 'John Doe')
        ->get();

    $this->assertCount(0, $result);
});

test('can delete with integer comparison', function () {
    $this->db->delete('test_delete')
        ->where('age', '=', 30)
        ->execute();

    $result = $this->db->select('*')
        ->from('test_delete')
        ->where('age', '=', 30)
        ->get();

    $this->assertCount(0, $result);
});

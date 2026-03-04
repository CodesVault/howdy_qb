<?php

declare(strict_types=1);

beforeEach(function () {
    $this->db = $this->getQueryBuilder();

    $this->db->create('test_table')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->execute();

    $this->db->insert('test_table', [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ],
        [
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
        ],
    ])->execute();
});

afterEach(function () {
    try {
        $this->db->dropIfExists('test_table');
    } catch (Exception $e) {
    }
});

// ========== Truncate ==========

test('truncate removes all rows from table', function () {
    // Verify data exists
    $before = $this->db->select('*')
        ->from('test_table')
        ->get();
    $this->assertCount(3, $before);

    // Truncate the table
    $this->db->truncate('test_table');

    // Verify table is empty
    $after = $this->db->select('*')
        ->from('test_table')
        ->get();
    $this->assertCount(0, $after);
});

test('truncate preserves table structure', function () {
    $this->db->truncate('test_table');

    // Insert new data after truncate
    $this->db->insert('test_table', [
        [
            'name' => 'New User',
            'email' => 'new@example.com',
        ],
    ])->execute();

    $result = $this->db->select('name', 'email')
        ->from('test_table')
        ->get();

    $this->assertCount(1, $result);
    $this->assertEquals('New User', $result[0]['name']);
    $this->assertEquals('new@example.com', $result[0]['email']);
});

test('truncate on empty table does not throw error', function () {
    // First truncate to empty the table
    $this->db->truncate('test_table');

    // Second truncate on already empty table should not error
    $this->db->truncate('test_table');

    $result = $this->db->select('*')
        ->from('test_table')
        ->get();
    $this->assertCount(0, $result);
});

// ========== Drop ==========

test('drop removes the table completely', function () {
    $this->db->drop('test_table');

    $this->expectException(Exception::class);
    $this->db->select('*')
        ->from('test_table')
        ->get();
});

test('drop on non-existent table throws exception', function () {
    $this->expectException(Exception::class);
    $this->db->drop('non_existent_table_xyz');
});

// ========== Drop If Exists ==========

test('dropIfExists removes existing table', function () {
    $this->db->dropIfExists('test_table');

    $this->expectException(Exception::class);
    $this->db->select('*')
        ->from('test_table')
        ->get();
});

test('dropIfExists on non-existent table does not throw', function () {
    // Should not throw any exception
    $this->db->dropIfExists('non_existent_table_xyz');

    // If we reach here, the test passes
    $this->assertTrue(true);
});

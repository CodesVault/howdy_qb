<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
    $this->db = $this->getQueryBuilder();

    // Create a base table for alter testing
    $this->db->create('test_alter')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->execute();
});

afterEach(function () {
    // Clean up test tables after each test
    try {
        $this->db->dropIfExists('test_alter');
    } catch (Exception $e) {
        // Table might not exist, ignore
    }
});

test('alter method chains correctly', function () {
    $alter = $this->db->alter('test_alter')
        ->add('age')->int();

    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Alter::class, $alter);
});

test('getSql returns correct alter query structure', function () {
    $sql = $this->db->alter('test_alter')
        ->add('age')->int()
        ->getSql();

    $this->assertIsArray($sql);
    $this->assertArrayHasKey('query', $sql);
    $this->assertStringContainsString('ALTER TABLE', $sql['query']);
    $this->assertStringContainsString('ADD age INT(255)', $sql['query']);
});

test('can add new column to existing table', function () {
    // Add a new column
    $this->db->alter('test_alter')
        ->add('age')->int()
        ->execute();

    // Insert data with the new column
    $this->db->insert('test_alter', [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30
        ]
    ]);

    // Verify the new column exists and data was inserted
    $result = $this->db->select('*')
        ->from('test_alter')
        ->where('email', '=', 'john@example.com')
        ->get();

    $this->assertCount(1, $result);
    $this->assertEquals(30, (int)$result[0]['age']);
});

test('can add string column to table', function () {
    // Add a string column
    $this->db->alter('test_alter')
        ->add('country')->string(50)
        ->execute();

    // Insert data
    $this->db->insert('test_alter', [
        [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'country' => 'USA'
        ]
    ]);

    $result = $this->db->select('country')
        ->from('test_alter')
        ->where('email', '=', 'jane@example.com')
        ->get();

    $this->assertEquals('USA', $result[0]['country']);
});

test('can drop column from table', function () {
    // First add a column
    $this->db->alter('test_alter')
        ->add('temp_column')->string(50)
        ->execute();

    // Insert data with temp column
    $this->db->insert('test_alter', [
        [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'temp_column' => 'temp_value'
        ]
    ]);

    // Now drop the column
    $this->db->alter('test_alter')
        ->drop('temp_column')
        ->execute();

    // Verify column was dropped by checking columns
    $result = $this->db->select('*')
        ->from('test_alter')
        ->where('email', '=', 'test@example.com')
        ->get();

    $this->assertArrayNotHasKey('temp_column', $result[0]);
});

test('can add text column to table', function () {
    $this->db->alter('test_alter')
        ->add('description')->text()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'description' => 'This is a long description'
        ]
    ]);

    $result = $this->db->select('description')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals('This is a long description', $result[0]['description']);
});

test('can add boolean column to table', function () {
    $this->db->alter('test_alter')
        ->add('is_active')->boolean()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'is_active' => true
        ]
    ]);

    $result = $this->db->select('is_active')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals(1, (int)$result[0]['is_active']);
});

test('can add date column to table', function () {
    $this->db->alter('test_alter')
        ->add('birth_date')->date()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'birth_date' => '1990-05-15'
        ]
    ]);

    $result = $this->db->select('birth_date')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals('1990-05-15', $result[0]['birth_date']);
});

test('can add datetime column to table', function () {
    $this->db->alter('test_alter')
        ->add('created_at')->dateTime()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'created_at' => '2024-01-15 10:30:00'
        ]
    ]);

    $result = $this->db->select('created_at')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertStringContainsString('2024-01-15', $result[0]['created_at']);
});

test('can add timestamp column with default now', function () {
    $this->db->alter('test_alter')
        ->add('updated_at')->timestamp('now')
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com'
        ]
    ]);

    $result = $this->db->select('updated_at')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    // Verify timestamp was automatically set
    $this->assertNotEmpty($result[0]['updated_at']);
});

test('can add decimal column to table', function () {
    $this->db->alter('test_alter')
        ->add('price')->decimal(10, 2)
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'Product',
            'email' => 'product@example.com',
            'price' => 99.99
        ]
    ]);

    $result = $this->db->select('price')
        ->from('test_alter')
        ->where('email', '=', 'product@example.com')
        ->get();

    $this->assertEquals('99.99', $result[0]['price']);
});

test('can add float column to table', function () {
    $this->db->alter('test_alter')
        ->add('weight')->float()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'Item',
            'email' => 'item@example.com',
            'weight' => 15.5
        ]
    ]);

    $result = $this->db->select('weight')
        ->from('test_alter')
        ->where('email', '=', 'item@example.com')
        ->get();

    $this->assertNotNull($result[0]['weight']);
});

test('can add double column to table', function () {
    $this->db->alter('test_alter')
        ->add('amount')->double()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'Transaction',
            'email' => 'trans@example.com',
            'amount' => 1234.5678
        ]
    ]);

    $result = $this->db->select('amount')
        ->from('test_alter')
        ->where('email', '=', 'trans@example.com')
        ->get();

    $this->assertNotNull($result[0]['amount']);
});

test('can add enum column to table', function () {
    $this->db->alter('test_alter')
        ->add('status')->enum(['active', 'inactive', 'pending'])
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'status' => 'active'
        ]
    ]);

    $result = $this->db->select('status')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals('active', $result[0]['status']);
});

test('can add column with default value', function () {
    $this->db->alter('test_alter')
        ->add('role')->string(20)->default('user')
        ->execute();

    // Insert without role - should use default
    $this->db->insert('test_alter', [
        [
            'name' => 'New User',
            'email' => 'newuser@example.com'
        ]
    ]);

    $result = $this->db->select('role')
        ->from('test_alter')
        ->where('email', '=', 'newuser@example.com')
        ->get();

    $this->assertEquals('user', $result[0]['role']);
});

test('can add required column to table', function () {
    $this->db->alter('test_alter')
        ->add('phone')->string(20)->required()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'phone' => '123-456-7890'
        ]
    ]);

    $result = $this->db->select('phone')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals('123-456-7890', $result[0]['phone']);
});

test('can add unsigned integer column', function () {
    $this->db->alter('test_alter')
        ->add('points')->int()->unsigned()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'points' => 100
        ]
    ]);

    $result = $this->db->select('points')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals(100, (int)$result[0]['points']);
});

test('can add bigint column to table', function () {
    $this->db->alter('test_alter')
        ->add('large_number')->bigInt()
        ->execute();

    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'large_number' => 9999999999
        ]
    ]);

    $result = $this->db->select('large_number')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertEquals(9999999999, (int)$result[0]['large_number']);
});

test('can add json column to table', function () {
    $this->db->alter('test_alter')
        ->add('metadata')->json()
        ->execute();

    $jsonData = '{"key": "value", "number": 123}';
    $this->db->insert('test_alter', [
        [
            'name' => 'User',
            'email' => 'user@example.com',
            'metadata' => $jsonData
        ]
    ]);

    $result = $this->db->select('metadata')
        ->from('test_alter')
        ->where('email', '=', 'user@example.com')
        ->get();

    $this->assertNotEmpty($result[0]['metadata']);
});

test('can update existing column type', function () {
	// Change 'name' column from string to text
	$this->db->alter('test_alter')
		->modify('name')->text()
		->execute();

	// Insert data with long name
	$longName = str_repeat('A', 10000);
	$this->db->insert('test_alter', [
		[
			'name' => $longName,
			'email' => 'user@example.com'
		]
	]);

	$result = $this->db->select('name')
		->from('test_alter')
		->where('email', '=', 'user@example.com')
		->get();

	$this->assertEquals($longName, $result[0]['name']);
});

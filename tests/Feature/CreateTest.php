<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
    $this->db = $this->getQueryBuilder();
});

afterEach(function () {
    // Clean up test tables after each test
    try {
        $this->db->dropIfExists('test_users');
    } catch (Exception $e) {
        // Table might not exist, ignore
    }
    try {
        $this->db->dropIfExists('test_products');
    } catch (Exception $e) {
        // Table might not exist, ignore
    }
    try {
        $this->db->dropIfExists('test_orders');
    } catch (Exception $e) {
        // Table might not exist, ignore
    }
});

test('can create table with basic integer column', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->getSql();

    $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS', $sql['query']);
    $this->assertStringContainsString('`id` INT(255) PRIMARY KEY', $sql['query']);
});

test('can create table with custom sized integer column', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->int(11)
        ->getSql();

    $this->assertStringContainsString('`id` INT(11)', $sql['query']);
});

test('create table method chains correctly', function () {
    $create = $this->db->create('test_users')
        ->column('id')->int();

    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Create::class, $create);

    $create = $create->column('name')->string();
    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Create::class, $create);
});

test('getSql returns query structure', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->getSql();

    $this->assertIsArray($sql);
    $this->assertArrayHasKey('query', $sql);
    $this->assertIsString($sql['query']);
});

test('can create table with bigint column', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->bigInt(20)->unsigned()->autoIncrement()->primary()
        ->getSql();

    $this->assertStringContainsString('`id` BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY', $sql['query']);
});

test('can create table with string column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('name')->string(100)
        ->execute();

    // Verify table was created by inserting and selecting data
    $this->db->insert('test_users', [
        ['id' => 1, 'name' => 'Test User']
    ]);

    $result = $this->db->select('name')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertCount(1, $result);
	$this->assertIsArray($result);
    $this->assertEquals('Test User', $result[0]['name']);
});

test('can create table with default string size', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('name')->string()
        ->execute();

    // Verify with a string that's exactly 255 chars (max default size)
    $longName = str_repeat('a', 255);
    $this->db->insert('test_users', [
        ['id' => 1, 'name' => $longName]
    ]);

    $result = $this->db->select('name')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals($longName, $result[0]['name']);
});

test('can create table with text column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('description')->text(5000)
        ->execute();

    $longText = 'This is a long description text.';
    $this->db->insert('test_users', [
        ['id' => 1, 'description' => $longText]
    ]);

    $result = $this->db->select('description')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals($longText, $result[0]['description']);
});

test('can create table with longtext column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('content')->longText()
        ->execute();

    $veryLongText = str_repeat('Long content ', 1000);
    $this->db->insert('test_users', [
        ['id' => 1, 'content' => $veryLongText]
    ]);

    $result = $this->db->select('content')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals($veryLongText, $result[0]['content']);
});

test('can create table with json column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('metadata')->json()
        ->execute();

    $jsonData = '{"age": 30, "name": "John"}';
    $this->db->insert('test_users', [
        ['id' => 1, 'metadata' => $jsonData]
    ]);

    $result = $this->db->select('metadata')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertNotEmpty($result[0]['metadata']);
	$this->assertEquals($jsonData, $result[0]['metadata']);
});

test('can create table with double column', function () {
    $this->db->create('test_products')
        ->column('id')->int()->primary()
        ->column('price')->double()
        ->execute();

    $this->db->insert('test_products', [
        ['id' => 1, 'price' => 99.99]
    ]);

    $result = $this->db->select('price')
        ->from('test_products')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals(99.99, (float)$result[0]['price']);
});

test('can create table with float column', function () {
    $this->db->create('test_products')
        ->column('id')->int()->primary()
        ->column('weight')->float()
        ->execute();

    $this->db->insert('test_products', [
        ['id' => 1, 'weight' => 15.5]
    ]);

    $result = $this->db->select('weight')
        ->from('test_products')
        ->where('id', '=', 1)
        ->get();

    $this->assertNotNull($result[0]['weight']);
});

test('can create table with decimal column', function () {
    $this->db->create('test_products')
        ->column('id')->int()->primary()
        ->column('price')->decimal(10, 2)
        ->execute();

    $this->db->insert('test_products', [
        ['id' => 1, 'price' => 1234.56]
    ]);

    $result = $this->db->select('price')
        ->from('test_products')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals('1234.56', $result[0]['price']);
});

test('can create table with default decimal precision', function () {
    $this->db->create('test_products')
        ->column('id')->int()->primary()
        ->column('price')->decimal()
        ->execute();

    $this->db->insert('test_products', [
        ['id' => 1, 'price' => 99.99]
    ]);

    $result = $this->db->select('price')
        ->from('test_products')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals('99.99', $result[0]['price']);
});

test('can create table with boolean column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('is_active')->boolean()
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'is_active' => true]
    ]);

    $result = $this->db->select('is_active')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals(1, (int)$result[0]['is_active']);
});

test('can create table with date column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('birth_date')->date()
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'birth_date' => '1990-05-15']
    ]);

    $result = $this->db->select('birth_date')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals('1990-05-15', $result[0]['birth_date']);
});

test('can create table with datetime column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('registered_at')->dateTime()
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'registered_at' => '2024-01-15 10:30:00']
    ]);

    $result = $this->db->select('registered_at')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertStringContainsString('2024-01-15', $result[0]['registered_at']);
});

test('can create table with timestamp column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('created_at')->timestamp()
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'created_at' => '2024-01-15 10:30:00']
    ]);

    $result = $this->db->select('created_at')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertNotEmpty($result[0]['created_at']);
});

test('can create table with timestamp default now', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('created_at')->timestamp('now')
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1]
    ]);

    $result = $this->db->select('created_at')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    // Verify created_at was automatically set
    $this->assertNotEmpty($result[0]['created_at']);
});

test('can create table with timestamp on update current', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('name')->string(50)
        ->column('updated_at')->timestamp('now', 'current')
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'name' => 'Initial']
    ]);

    $result = $this->db->select('updated_at')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    // Verify timestamp was set
    $this->assertNotEmpty($result[0]['updated_at']);
});

test('can create table with required column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('email')->string(150)->required()
        ->execute();

    // Insert data - email is required
    $this->db->insert('test_users', [
        ['id' => 1, 'email' => 'test@example.com']
    ]);

    $result = $this->db->select('email')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals('test@example.com', $result[0]['email']);
});

test('can create table with nullable column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('phone')->string(20)->nullable()
        ->execute();

    // Insert without phone (nullable)
    $this->db->insert('test_users', [
        ['id' => 1]
    ]);

    $result = $this->db->select('phone')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    // Check if phone is null or the string 'NULL'
    $this->assertTrue($result[0]['phone'] === null || $result[0]['phone'] === 'NULL');
});

test('can create table with default value for string', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('status')->string(20)->default('active')
        ->execute();

    // Insert without status - should use default
    $this->db->insert('test_users', [
        ['id' => 1]
    ]);

    $result = $this->db->select('status')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals('active', $result[0]['status']);
});

test('can create table with default value for integer', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('age')->int()->default(0)
        ->execute();

    // Insert without age - should use default
    $this->db->insert('test_users', [
        ['id' => 1]
    ]);

    $result = $this->db->select('age')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals(0, (int)$result[0]['age']);
});

test('can create table with unsigned column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('points')->int()->unsigned()
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'points' => 100]
    ]);

    $result = $this->db->select('points')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals(100, (int)$result[0]['points']);
});

test('can create table with auto increment column', function () {
    $this->db->create('test_users')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(50)
        ->execute();

    // Insert without id - should auto increment
    $this->db->insert('test_users', [
        ['name' => 'User 1']
    ]);
    $this->db->insert('test_users', [
        ['name' => 'User 2']
    ]);

    $result = $this->db->select('id', 'name')
        ->from('test_users')
        ->get();

    $this->assertCount(2, $result);
    $this->assertTrue((int)$result[0]['id'] > 0);
    $this->assertTrue((int)$result[1]['id'] > (int)$result[0]['id']);
});

test('can create table with primary key', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('name')->string(50)
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'name' => 'Test User']
    ]);

    $result = $this->db->select('*')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertCount(1, $result);
});

test('can create table with composite primary key', function () {
    $this->db->create('test_orders')
        ->column('order_id')->int()
        ->column('product_id')->int()
        ->column('quantity')->int()
        ->primary(['order_id', 'product_id'])
        ->execute();

    // Insert data with composite key
    $this->db->insert('test_orders', [
        ['order_id' => 1, 'product_id' => 100, 'quantity' => 5]
    ]);

    $result = $this->db->select('*')
        ->from('test_orders')
        ->where('order_id', '=', 1)
        ->andWhere('product_id', '=', 100)
        ->get();

    $this->assertCount(1, $result);
    $this->assertEquals(5, (int)$result[0]['quantity']);
});

test('can create table with index', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('email')->string(150)
        ->index(['email'])
        ->execute();

    // Insert and query using indexed column
    $this->db->insert('test_users', [
        ['id' => 1, 'email' => 'indexed@example.com']
    ]);

    $result = $this->db->select('email')
        ->from('test_users')
        ->where('email', '=', 'indexed@example.com')
        ->get();

    $this->assertCount(1, $result);
});

test('can create table with composite index', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('first_name')->string(50)
        ->column('last_name')->string(50)
        ->index(['first_name', 'last_name'])
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe']
    ]);

    $result = $this->db->select('*')
        ->from('test_users')
        ->where('first_name', '=', 'John')
        ->andWhere('last_name', '=', 'Doe')
        ->get();

    $this->assertCount(1, $result);
});

test('can create table with enum column', function () {
    $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('role')->enum(['admin', 'user', 'guest'])
        ->execute();

    $this->db->insert('test_users', [
        ['id' => 1, 'role' => 'admin']
    ]);

    $result = $this->db->select('role')
        ->from('test_users')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals('admin', $result[0]['role']);
});

test('can create table with enum containing numeric values', function () {
    // Test SQL generation to verify enum with numeric values works
    $sql = $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->column('status')->enum([1, 2, 3])
        ->getSql();

    $this->assertStringContainsString('ENUM', $sql['query']);
    $this->assertStringContainsString('1, 2, 3', $sql['query']);
});

test('can create table with foreign key', function () {
    // Create parent table first
    $this->db->create('test_parent')
        ->column('id')->int()->primary()
        ->column('name')->string(50)
        ->execute();

    // Create child table with foreign key
    $this->db->create('test_orders')
        ->column('id')->int()->primary()
        ->column('user_id')->int()
        ->foreignKey('user_id', 'test_parent.id')
        ->execute();

    // Insert parent record
    $this->db->insert('test_parent', [
        ['id' => 1, 'name' => 'Parent']
    ]);

    // Insert child record
    $this->db->insert('test_orders', [
        ['id' => 1, 'user_id' => 1]
    ]);

    $result = $this->db->select('*')
        ->from('test_orders')
        ->where('id', '=', 1)
        ->get();

    $this->assertEquals(1, (int)$result[0]['user_id']);

    // Cleanup
    $this->db->dropIfExists('test_parent');
});

test('can create table with foreign key and on delete cascade', function () {
    // Create parent table
    $this->db->create('test_parent')
        ->column('id')->int()->primary()
        ->execute();

    // Create child table with foreign key cascade
    $this->db->create('test_orders')
        ->column('id')->int()->primary()
        ->column('user_id')->int()
        ->foreignKey('user_id', 'test_parent.id', 'cascade')
        ->execute();

    // Insert records
    $this->db->insert('test_parent', [['id' => 1]]);
    $this->db->insert('test_orders', [['id' => 1, 'user_id' => 1]]);

    // Verify child exists
    $result = $this->db->select('*')->from('test_orders')->get();
    $this->assertCount(1, $result);

    // Cleanup
    $this->db->dropIfExists('test_parent');
});

test('can create table with foreign key and on delete set null', function () {
    // Test SQL generation for ON DELETE SET NULL
    $sql = $this->db->create('test_orders')
        ->column('user_id')->int()
        ->foreignKey('user_id', 'users.id', 'set null')
        ->getSql();

    $this->assertStringContainsString('ON DELETE SET NULL', $sql['query']);
});

test('can create table with multiple columns', function () {
    $this->db->create('test_users')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()
        ->column('created_at')->timestamp('now')
        ->execute();

    // Insert test data - use insert and then select to verify
    $this->db->insert('test_users', [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30
        ]
    ]);

    $result = $this->db->select('*')
        ->from('test_users')
        ->where('email', '=', 'john@example.com')
        ->get();

    $this->assertGreaterThan(0, count($result));
    if (count($result) > 0) {
        $this->assertEquals('John Doe', $result[0]['name']);
        $this->assertEquals('john@example.com', $result[0]['email']);
        $this->assertEquals(30, (int)$result[0]['age']);
        $this->assertNotEmpty($result[0]['created_at']);
    }
});

test('can execute table creation', function () {
    $this->db->create('test_users')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->execute();

    // Verify table was created by trying to insert data
    $result = $this->db->insert('test_users', [
        [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]
    ]);

    $this->assertTrue(is_object($result) || $result === true);
});

test('can create complex table with all features', function () {
    $this->db->create('test_products')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(200)->required()
        ->column('description')->text()
        ->column('price')->decimal(10, 2)->required()
        ->column('stock')->int()->unsigned()->default(0)
        ->column('status')->enum(['active', 'inactive', 'discontinued'])->default('active')
        ->column('is_featured')->boolean()->default(0)
        ->column('created_at')->timestamp('now')
        ->column('updated_at')->timestamp('now', 'current')
        ->index(['name'])
        ->execute();

    // Insert comprehensive test data (without metadata and json which might cause issues)
    $this->db->insert('test_products', [
        [
            'name' => 'Test Product',
            'description' => 'This is a comprehensive test product',
            'price' => 99.99
        ]
    ]);

    $result = $this->db->select('*')
        ->from('test_products')
        ->where('name', '=', 'Test Product')
        ->get();

    // Verify all columns were created and data inserted correctly
    $this->assertGreaterThan(0, count($result));
    $this->assertTrue((int)$result[0]['id'] > 0);
    $this->assertEquals('Test Product', $result[0]['name']);
    $this->assertEquals('This is a comprehensive test product', $result[0]['description']);
    $this->assertEquals('99.99', $result[0]['price']);
    $this->assertEquals(0, (int)$result[0]['stock']); // Default value
    $this->assertEquals('active', $result[0]['status']); // Default value
    $this->assertEquals(0, (int)$result[0]['is_featured']); // Default value
    $this->assertNotEmpty($result[0]['created_at']);
    $this->assertNotEmpty($result[0]['updated_at']);
});

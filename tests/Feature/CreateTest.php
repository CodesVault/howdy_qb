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
    $sql = $this->db->create('test_users')
        ->column('name')->string(100)
        ->getSql();

    $this->assertStringContainsString('`name` VARCHAR(100)', $sql['query']);
});

test('can create table with default string size', function () {
    $sql = $this->db->create('test_users')
        ->column('name')->string()
        ->getSql();

    $this->assertStringContainsString('`name` VARCHAR(255)', $sql['query']);
});

test('can create table with text column', function () {
    $sql = $this->db->create('test_users')
        ->column('description')->text(5000)
        ->getSql();

    $this->assertStringContainsString('`description` TEXT(5000)', $sql['query']);
});

test('can create table with longtext column', function () {
    $sql = $this->db->create('test_users')
        ->column('content')->longText()
        ->getSql();

    $this->assertStringContainsString('`content` LONGTEXT', $sql['query']);
});

test('can create table with json column', function () {
    $sql = $this->db->create('test_users')
        ->column('metadata')->json()
        ->getSql();

    $this->assertStringContainsString('`metadata` JSON', $sql['query']);
});

test('can create table with double column', function () {
    $sql = $this->db->create('test_products')
        ->column('price')->double()
        ->getSql();

    $this->assertStringContainsString('`price` DOUBLE', $sql['query']);
});

test('can create table with float column', function () {
    $sql = $this->db->create('test_products')
        ->column('weight')->float()
        ->getSql();

    $this->assertStringContainsString('`weight` FLOAT', $sql['query']);
});

test('can create table with decimal column', function () {
    $sql = $this->db->create('test_products')
        ->column('price')->decimal(10, 2)
        ->getSql();

    $this->assertStringContainsString('`price` DECIMAL(10, 2)', $sql['query']);
});

test('can create table with default decimal precision', function () {
    $sql = $this->db->create('test_products')
        ->column('price')->decimal()
        ->getSql();

    $this->assertStringContainsString('`price` DECIMAL(8, 2)', $sql['query']);
});

test('can create table with boolean column', function () {
    $sql = $this->db->create('test_users')
        ->column('is_active')->boolean()
        ->getSql();

    $this->assertStringContainsString('`is_active` BOOLEAN', $sql['query']);
});

test('can create table with date column', function () {
    $sql = $this->db->create('test_users')
        ->column('birth_date')->date()
        ->getSql();

    $this->assertStringContainsString('`birth_date` DATE', $sql['query']);
});

test('can create table with datetime column', function () {
    $sql = $this->db->create('test_users')
        ->column('registered_at')->dateTime()
        ->getSql();

    $this->assertStringContainsString('`registered_at` DATETIME', $sql['query']);
});

test('can create table with timestamp column', function () {
    $sql = $this->db->create('test_users')
        ->column('created_at')->timestamp()
        ->getSql();

    $this->assertStringContainsString('`created_at` TIMESTAMP', $sql['query']);
});

test('can create table with timestamp default now', function () {
    $sql = $this->db->create('test_users')
        ->column('created_at')->timestamp('now')
        ->getSql();

    $this->assertStringContainsString('`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP', $sql['query']);
});

test('can create table with timestamp on update current', function () {
    $sql = $this->db->create('test_users')
        ->column('updated_at')->timestamp('now', 'current')
        ->getSql();

    $this->assertStringContainsString('DEFAULT CURRENT_TIMESTAMP', $sql['query']);
    $this->assertStringContainsString('ON UPDATE CURRENT_TIMESTAMP', $sql['query']);
});

test('can create table with required column', function () {
    $sql = $this->db->create('test_users')
        ->column('email')->string(150)->required()
        ->getSql();

    $this->assertStringContainsString('`email` VARCHAR(150) NOT NULL', $sql['query']);
});

test('can create table with nullable column', function () {
    $sql = $this->db->create('test_users')
        ->column('phone')->string(20)->nullable()
        ->getSql();

    $this->assertStringContainsString("`phone` VARCHAR(20) DEFAULT 'NULL'", $sql['query']);
});

test('can create table with default value for string', function () {
    $sql = $this->db->create('test_users')
        ->column('status')->string(20)->default('active')
        ->getSql();

    $this->assertStringContainsString('`status` VARCHAR(20) DEFAULT \'active\'', $sql['query']);
});

test('can create table with default value for integer', function () {
    $sql = $this->db->create('test_users')
        ->column('age')->int()->default(0)
        ->getSql();

    $this->assertStringContainsString('`age` INT(255) DEFAULT 0', $sql['query']);
});

test('can create table with unsigned column', function () {
    $sql = $this->db->create('test_users')
        ->column('points')->int()->unsigned()
        ->getSql();

    $this->assertStringContainsString('`points` INT(255) UNSIGNED', $sql['query']);
});

test('can create table with auto increment column', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->getSql();

    $this->assertStringContainsString('AUTO_INCREMENT', $sql['query']);
});

test('can create table with primary key', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->int()->primary()
        ->getSql();

    $this->assertStringContainsString('PRIMARY KEY', $sql['query']);
});

test('can create table with composite primary key', function () {
    $sql = $this->db->create('test_orders')
        ->column('order_id')->int()
        ->column('product_id')->int()
        ->primary(['order_id', 'product_id'])
        ->getSql();

    $this->assertStringContainsString('PRIMARY KEY (order_id,product_id)', $sql['query']);
});

test('can create table with index', function () {
    $sql = $this->db->create('test_users')
        ->column('email')->string(150)
        ->index(['email'])
        ->getSql();

    $this->assertStringContainsString('INDEX (email)', $sql['query']);
});

test('can create table with composite index', function () {
    $sql = $this->db->create('test_users')
        ->column('first_name')->string(50)
        ->column('last_name')->string(50)
        ->index(['first_name', 'last_name'])
        ->getSql();

    $this->assertStringContainsString('INDEX (first_name,last_name)', $sql['query']);
});

test('can create table with enum column', function () {
    $sql = $this->db->create('test_users')
        ->column('role')->enum(['admin', 'user', 'guest'])
        ->getSql();

    $this->assertStringContainsString('`role` ENUM(\'admin\', \'user\', \'guest\')', $sql['query']);
});

test('can create table with enum containing numeric values', function () {
    $sql = $this->db->create('test_users')
        ->column('status')->enum([1, 2, 3])
        ->getSql();

    $this->assertStringContainsString('`status` ENUM(1, 2, 3)', $sql['query']);
});

test('can create table with foreign key', function () {
    $sql = $this->db->create('test_orders')
        ->column('user_id')->int()
        ->foreignKey('user_id', 'users.id')
        ->getSql();

    $this->assertStringContainsString('FOREIGN KEY (user_id) REFERENCES', $sql['query']);
    $this->assertStringContainsString('users (id)', $sql['query']);
});

test('can create table with foreign key and on delete cascade', function () {
    $sql = $this->db->create('test_orders')
        ->column('user_id')->int()
        ->foreignKey('user_id', 'users.id', 'cascade')
        ->getSql();

    $this->assertStringContainsString('FOREIGN KEY (user_id) REFERENCES', $sql['query']);
    $this->assertStringContainsString('ON DELETE CASCADE', $sql['query']);
});

test('can create table with foreign key and on delete set null', function () {
    $sql = $this->db->create('test_orders')
        ->column('user_id')->int()
        ->foreignKey('user_id', 'users.id', 'set null')
        ->getSql();

    $this->assertStringContainsString('ON DELETE SET NULL', $sql['query']);
});

test('can create table with multiple columns', function () {
    $sql = $this->db->create('test_users')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()->nullable()
        ->column('created_at')->timestamp('now')
        ->getSql();

    $this->assertStringContainsString('`id` BIGINT(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY', $sql['query']);
    $this->assertStringContainsString('`name` VARCHAR(100) NOT NULL', $sql['query']);
    $this->assertStringContainsString('`email` VARCHAR(150) NOT NULL', $sql['query']);
    $this->assertStringContainsString("`age` INT(255) DEFAULT 'NULL'", $sql['query']);
    $this->assertStringContainsString('`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP', $sql['query']);
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
    $sql = $this->db->create('test_products')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(200)->required()
        ->column('description')->text()
        ->column('price')->decimal(10, 2)->required()
        ->column('stock')->int()->unsigned()->default(0)
        ->column('status')->enum(['active', 'inactive', 'discontinued'])->default('active')
        ->column('is_featured')->boolean()->default(0)
        ->column('metadata')->json()->nullable()
        ->column('created_at')->timestamp('now')
        ->column('updated_at')->timestamp('now', 'current')
        ->index(['name, status'])
        ->getSql();

    $this->assertStringContainsString("CREATE TABLE IF NOT EXISTS", $sql['query']);
    $this->assertStringContainsString('`id` BIGINT(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY', $sql['query']);
    $this->assertStringContainsString('`name` VARCHAR(200) NOT NULL', $sql['query']);
    $this->assertStringContainsString('`description` TEXT(10000)', $sql['query']);
    $this->assertStringContainsString('`price` DECIMAL(10, 2) NOT NULL', $sql['query']);
    $this->assertStringContainsString('`stock` INT(255) UNSIGNED DEFAULT 0', $sql['query']);
    $this->assertStringContainsString("`status` ENUM('active', 'inactive', 'discontinued') DEFAULT 'active'", $sql['query']);
    $this->assertStringContainsString('`is_featured` BOOLEAN DEFAULT 0', $sql['query']);
    $this->assertStringContainsString("`metadata` JSON DEFAULT 'NULL'", $sql['query']);
    $this->assertStringContainsString('INDEX (name, status)', $sql['query']);
});

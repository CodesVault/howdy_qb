<?php

declare(strict_types=1);

use CodesVault\Howdyqb\Validation\IdentifierValidator;

// ==================== validateTableName Tests ====================

test('validateTableName returns escaped identifier for valid names', function () {
    $validNames = [
        'users'          => '`users`',
        'user_posts'     => '`user_posts`',
        'UserPosts'      => '`UserPosts`',
        '_private_table' => '`_private_table`',
        'table123'       => '`table123`',
        'a'              => '`a`',
        'A'              => '`A`',
        '_'              => '`_`',
    ];

    foreach ($validNames as $name => $expected) {
        expect(IdentifierValidator::validateTableName($name))->toBe($expected);
    }
});

test('validateTableName rejects identifiers with special characters', function () {
    $invalidNames = [
        'users;DROP TABLE users',
        "users' OR '1'='1",
        'users--',
        'users/*comment*/',
        'users`injection`',
        'table name',
        'table-name',
        'table@name',
    ];

    foreach ($invalidNames as $name) {
        expect(fn () => IdentifierValidator::validateTableName($name))
            ->toThrow(InvalidArgumentException::class);
    }
});

test('validateTableName rejects identifiers starting with numbers', function () {
    expect(fn () => IdentifierValidator::validateTableName('123table'))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => IdentifierValidator::validateTableName('1_users'))
        ->toThrow(InvalidArgumentException::class);
});

test('validateTableName rejects standalone SQL keywords', function () {
    $keywords = ['SELECT', 'DROP', 'UNION', 'DELETE', 'INSERT', 'UPDATE', 'EXEC'];

    foreach ($keywords as $name) {
        expect(fn () => IdentifierValidator::validateTableName($name))
            ->toThrow(InvalidArgumentException::class);
    }
});

test('validateTableName allows embedded SQL keywords', function () {
    $validNames = [
        'usersSELECT'  => '`usersSELECT`',
        'DROP_table'   => '`DROP_table`',
        'tableUNION'   => '`tableUNION`',
        'DELETEusers'  => '`DELETEusers`',
    ];

    foreach ($validNames as $name => $expected) {
        expect(IdentifierValidator::validateTableName($name))->toBe($expected);
    }
});

test('validateTableName rejects name exceeding max length', function () {
    $longName = str_repeat('a', 65);
    expect(fn () => IdentifierValidator::validateTableName($longName))
        ->toThrow(InvalidArgumentException::class);
});

test('validateTableName rejects empty string', function () {
    expect(fn () => IdentifierValidator::validateTableName(''))
        ->toThrow(InvalidArgumentException::class);
});

// ==================== validateColumnName Tests ====================

test('validateColumnName returns escaped identifier', function () {
    expect(IdentifierValidator::validateColumnName('email'))->toBe('`email`');
    expect(IdentifierValidator::validateColumnName('user_name'))->toBe('`user_name`');
});

test('validateColumnName throws exception for empty name', function () {
    IdentifierValidator::validateColumnName('');
})->throws(InvalidArgumentException::class, 'Column name cannot be empty');

test('validateColumnName returns wildcard as-is', function () {
    expect(IdentifierValidator::validateColumnName('*'))->toBe('*');
});

test('validateColumnName handles table.column syntax', function () {
    expect(IdentifierValidator::validateColumnName('users.id'))->toBe('`users`.`id`');
    expect(IdentifierValidator::validateColumnName('posts.title'))->toBe('`posts`.`title`');
});

test('validateColumnName rejects multiple dots', function () {
    IdentifierValidator::validateColumnName('db.users.id');
})->throws(InvalidArgumentException::class, 'Invalid column name format');

test('validateColumnName rejects name exceeding max length', function () {
    $longName = str_repeat('a', 65);
    IdentifierValidator::validateColumnName($longName);
})->throws(InvalidArgumentException::class, 'exceeds maximum length');

test('validateColumnName rejects invalid column names', function () {
    $invalidNames = ['123col', 'col name', 'col;drop', 'col--'];

    foreach ($invalidNames as $name) {
        expect(fn () => IdentifierValidator::validateColumnName($name))
            ->toThrow(InvalidArgumentException::class);
    }
});

// ==================== validateColumnNames Tests ====================

test('validateColumnNames validates array of column names', function () {
    $result = IdentifierValidator::validateColumnNames(['id', 'name', 'email']);
    expect($result)->toBe(['`id`', '`name`', '`email`']);
});

test('validateColumnNames handles empty array', function () {
    $result = IdentifierValidator::validateColumnNames([]);
    expect($result)->toBe([]);
});

test('validateColumnNames throws on first invalid column', function () {
    expect(fn () => IdentifierValidator::validateColumnNames(['id', '123invalid', 'email']))
        ->toThrow(InvalidArgumentException::class);
});

// ==================== validateTableNameWithAlias Tests ====================

test('validateTableNameWithAlias validates table with alias', function () {
    $result = IdentifierValidator::validateTableNameWithAlias('users u');
    expect($result)->toBeString();
    expect($result)->toContain('`u`');
});

test('validateTableNameWithAlias validates table without alias', function () {
    $result = IdentifierValidator::validateTableNameWithAlias('users');
    expect($result)->toBeString();
    expect($result)->toContain('`users`');
});

test('validateTableNameWithAlias rejects empty string', function () {
    expect(fn () => IdentifierValidator::validateTableNameWithAlias(''))
        ->toThrow(InvalidArgumentException::class, 'Table name cannot be empty');
});

test('validateTableNameWithAlias rejects whitespace-only string', function () {
    expect(fn () => IdentifierValidator::validateTableNameWithAlias('   '))
        ->toThrow(InvalidArgumentException::class, 'Table name cannot be empty');
});

test('validateTableNameWithAlias rejects invalid alias', function () {
    expect(fn () => IdentifierValidator::validateTableNameWithAlias('users 123bad'))
        ->toThrow(InvalidArgumentException::class);
});

test('validateTableNameWithAlias rejects invalid table name part', function () {
    // table name with special chars remains invalid even after prefix
    expect(fn () => IdentifierValidator::validateTableNameWithAlias('table;drop u'))
        ->toThrow(InvalidArgumentException::class);
});

// ==================== escapeIdentifier Tests ====================

test('escapeIdentifier wraps in backticks', function () {
    expect(IdentifierValidator::escapeIdentifier('users'))->toBe('`users`');
    expect(IdentifierValidator::escapeIdentifier('user_posts'))->toBe('`user_posts`');
});

test('escapeIdentifier removes existing backticks', function () {
    expect(IdentifierValidator::escapeIdentifier('`users`'))->toBe('`users`');
});

// ==================== validateOperator Tests ====================

test('validateOperator accepts valid comparison operators', function () {
    $operators = ['=', '!=', '<>', '<', '>', '<=', '>='];

    foreach ($operators as $op) {
        expect(IdentifierValidator::validateOperator($op))->toBe($op);
    }
});

test('validateOperator accepts valid keyword operators', function () {
    $operators = [
        'LIKE', 'NOT LIKE',
        'IN', 'NOT IN',
        'BETWEEN', 'NOT BETWEEN',
        'IS', 'IS NOT',
        'REGEXP', 'NOT REGEXP',
        'EXISTS', 'NOT EXISTS',
    ];

    foreach ($operators as $op) {
        expect(IdentifierValidator::validateOperator($op))->toBe($op);
    }
});

test('validateOperator is case-insensitive', function () {
    expect(IdentifierValidator::validateOperator('like'))->toBe('LIKE');
    expect(IdentifierValidator::validateOperator('Not In'))->toBe('NOT IN');
    expect(IdentifierValidator::validateOperator('is not'))->toBe('IS NOT');
});

test('validateOperator trims whitespace', function () {
    expect(IdentifierValidator::validateOperator('  =  '))->toBe('=');
    expect(IdentifierValidator::validateOperator(' LIKE '))->toBe('LIKE');
});

test('validateOperator rejects empty string', function () {
    expect(fn () => IdentifierValidator::validateOperator(''))
        ->toThrow(InvalidArgumentException::class, 'Operator cannot be empty');
});

test('validateOperator rejects invalid operators', function () {
    $invalidOperators = [
        'DROP',
        'SELECT',
        '==',
        '===',
        '||',
        '&&',
        '; DROP TABLE',
        "' OR '1'='1",
    ];

    foreach ($invalidOperators as $op) {
        expect(fn () => IdentifierValidator::validateOperator($op))
            ->toThrow(InvalidArgumentException::class);
    }
});

// ==================== SQL Injection Prevention ====================

test('rejects common SQL injection patterns', function () {
    $reflection = new ReflectionMethod(IdentifierValidator::class, 'isValidIdentifier');

    $injectionPatterns = [
        "'; DROP TABLE users; --",
        "1' OR '1'='1",
        "1; SELECT * FROM users",
        "admin'--",
        "1' UNION SELECT * FROM passwords",
    ];

    foreach ($injectionPatterns as $pattern) {
        expect($reflection->invoke(null, $pattern))->toBeFalse(
            "Expected SQL injection pattern to be rejected: $pattern"
        );
    }
});

test('rejects hex injection attempts', function () {
    $reflection = new ReflectionMethod(IdentifierValidator::class, 'isValidIdentifier');

    expect($reflection->invoke(null, '0x414243'))->toBeFalse();
});

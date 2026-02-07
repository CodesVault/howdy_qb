<?php

declare(strict_types=1);

use CodesVault\Howdyqb\Validation\IdentifierValidator;

// ==================== validateTableName Tests ====================

test('validates valid identifiers', function () {
    $validNames = [
        'users',
        'user_posts',
        'UserPosts',
        '_private_table',
        'table123',
        'a',
        'A',
        '_',
    ];

    foreach ($validNames as $name) {
        expect(IdentifierValidator::validateTableName($name))->toBe($name);
    }
});

test('rejects identifiers with special characters', function () {
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

test('rejects identifiers starting with numbers', function () {
    expect(fn () => IdentifierValidator::validateTableName('123table'))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => IdentifierValidator::validateTableName('1_users'))
        ->toThrow(InvalidArgumentException::class);
});

test('rejects standalone SQL keywords as identifiers', function () {
    // Only standalone keywords at word boundaries are rejected
    $namesWithKeywords = [
        'SELECT',
        'DROP',
        'UNION',
        'DELETE',
    ];

    foreach ($namesWithKeywords as $name) {
        expect(fn () => IdentifierValidator::validateTableName($name))
            ->toThrow(InvalidArgumentException::class);
    }
});

test('allows embedded SQL keywords in identifiers', function () {
    // Embedded keywords are valid since they don't pose injection risk
    $validNames = [
        'usersSELECT',
        'DROP_table',
        'tableUNION',
        'DELETEusers',
    ];

    foreach ($validNames as $name) {
        expect(IdentifierValidator::validateTableName($name))->toBe($name);
    }
});

// ==================== validateColumnName Tests ====================

test('validateColumnName throws exception for empty name', function () {
    IdentifierValidator::validateColumnName('');
})->throws(InvalidArgumentException::class, 'Column name cannot be empty');

test('validateColumnName returns escaped identifier', function () {
    $result = IdentifierValidator::validateColumnName('email');
    expect($result)->toBe('`email`');
});

test('validates table.column syntax', function () {
    $result = IdentifierValidator::validateColumnName('users.id');
    expect($result)->toBe('`users`.`id`');
});

test('rejects invalid table.column syntax with multiple dots', function () {
    IdentifierValidator::validateColumnName('db.users.id');
})->throws(InvalidArgumentException::class, 'Invalid column name format');

test('rejects column name exceeding max length', function () {
    $longName = str_repeat('a', 65);
    IdentifierValidator::validateColumnName($longName);
})->throws(InvalidArgumentException::class, 'exceeds maximum length');

// ==================== escapeIdentifier Tests ====================

test('escapeIdentifier wraps in backticks', function () {
    expect(IdentifierValidator::escapeIdentifier('users'))->toBe('`users`');
    expect(IdentifierValidator::escapeIdentifier('user_posts'))->toBe('`user_posts`');
});

test('escapeIdentifier removes existing backticks', function () {
    expect(IdentifierValidator::escapeIdentifier('`users`'))->toBe('`users`');
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

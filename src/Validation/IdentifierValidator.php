<?php

namespace CodesVault\Howdyqb\Validation;

use CodesVault\Howdyqb\Utilities;
use InvalidArgumentException;

class IdentifierValidator
{
    private const VALID_IDENTIFIER_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

    private const MAX_IDENTIFIER_LENGTH = 64;

	private const VALID_OPERATORS = [
        '=', '!=', '<>', '<', '>', '<=', '>=',
        'LIKE', 'NOT LIKE',
        'IN', 'NOT IN',
        'BETWEEN', 'NOT BETWEEN',
        'IS', 'IS NOT',
        'REGEXP', 'NOT REGEXP',
        'EXISTS', 'NOT EXISTS',
    ];

	public static function validateTableName(string $tableName): string
    {
        if (strlen($tableName) > self::MAX_IDENTIFIER_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('Table name exceeds maximum length of %d characters.', self::MAX_IDENTIFIER_LENGTH)
            );
        }

        if (!self::isValidIdentifier($tableName)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid table name: "%s". Table names must start with a letter or underscore and contain only alphanumeric characters and underscores.', $tableName)
            );
        }
        return self::escapeIdentifier($tableName);
    }

    public static function validateColumnName(string $columnName): string
    {
        $columnName = trim($columnName);

        if (empty($columnName)) {
            throw new InvalidArgumentException('Column name cannot be empty.');
        }

        // Allow wildcard selector
        if ($columnName === '*') {
            return '*';
        }

        // Handle table.column syntax
        if (strpos($columnName, '.') !== false) {
            $parts = explode('.', $columnName);
            if (count($parts) !== 2) {
                throw new InvalidArgumentException(
                    sprintf('Invalid column name format: "%s". Use "table.column" or just "column".', $columnName)
                );
            }

            if (!self::isValidIdentifier($parts[0]) || !self::isValidIdentifier($parts[1])) {
                throw new InvalidArgumentException(
                    sprintf('Invalid column name: "%s".', $columnName)
                );
            }

            return self::escapeIdentifier($parts[0]) . '.' . self::escapeIdentifier($parts[1]);
        }

        if (strlen($columnName) > self::MAX_IDENTIFIER_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Column name exceeds maximum length of %d characters.', self::MAX_IDENTIFIER_LENGTH)
            );
        }

        if (!self::isValidIdentifier($columnName)) {
            throw new InvalidArgumentException(
                sprintf('Invalid column name: "%s". Column names must start with a letter or underscore and contain only alphanumeric characters and underscores.', $columnName)
            );
        }

        return self::escapeIdentifier($columnName);
    }

	public static function validateColumnNames(array $columnNames): array
	{
		$validatedColumns = [];
		foreach ($columnNames as $columnName) {
			$validatedColumns[] = self::validateColumnName($columnName);
		}
		return $validatedColumns;
	}

    private static function isValidIdentifier(string $identifier): bool
    {
        if (self::containsSqlInjectionPatterns($identifier)) {
            return false;
        }

        return preg_match(self::VALID_IDENTIFIER_PATTERN, $identifier) === 1;
    }

    private static function containsSqlInjectionPatterns(string $identifier): bool
    {
        $dangerousPatterns = [
            '/[;\'"\\\\]/',           // Semicolons, quotes, backslashes
            '/--/',                    // SQL comments
            '/\/\*/',                  // Block comment start
            '/\*\//',                  // Block comment end
            '/\bOR\b/i',              // OR keyword
            '/\bAND\b/i',             // AND keyword
            '/\bUNION\b/i',           // UNION keyword
            '/\bSELECT\b/i',          // SELECT keyword
            '/\bDROP\b/i',            // DROP keyword
            '/\bDELETE\b/i',          // DELETE keyword
            '/\bINSERT\b/i',          // INSERT keyword
            '/\bUPDATE\b/i',          // UPDATE keyword
            '/\bEXEC\b/i',            // EXEC keyword
            '/0x[0-9a-fA-F]+/',       // Hex values
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $identifier)) {
                return true;
            }
        }

        return false;
    }

    public static function escapeIdentifier(string $identifier): string
    {
        $identifier = str_replace('`', '', $identifier);
        return '`' . $identifier . '`';
    }

	public static function validateTableNameWithAlias(string $tableName): string
	{
		$tableName = trim($tableName);

		if (empty($tableName)) {
			throw new InvalidArgumentException('Table name cannot be empty.');
		}

		// Handle table AS alias or table alias syntax
		$parts = explode(' ', $tableName);
		if (count($parts) === 2) {
			$tablePart =  self::validateTableName(Utilities::get_db_configs()->prefix . $parts[0]);
			$aliasPart = self::validateTableName($parts[1]);

			return $tablePart . ' ' . $aliasPart;
		}

		return self::validateTableName($tableName);
	}

    public static function validateOperator(string $operator): string
    {
        $operator = trim(strtoupper($operator));

        if (empty($operator)) {
            throw new InvalidArgumentException('Operator cannot be empty.');
        }

        if (!in_array($operator, self::VALID_OPERATORS, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid operator: "%s".', $operator)
            );
        }

        return $operator;
    }
}

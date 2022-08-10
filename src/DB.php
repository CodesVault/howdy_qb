<?php

namespace CodesVault\WPqb;

use CodesVault\WPqb\Api\CreateInterface;
use CodesVault\WPqb\Api\SelectInterface;
use CodesVault\WPqb\Api\UpdateInterface;

class DB extends QueryFactory
{
    public static function select(...$columns): SelectInterface
    {
        $factory = new self(static::$driver);
        $query = $factory->selectQuery();
        $query = $query->columns(...$columns);
        return $query;
    }

    public static function insert(string $table_name, array $data)
    {
        $factory = new self(static::$driver);
        $factory->insertQuery($table_name, $data);
        return $factory;
    }

    public static function create(string $table_name): CreateInterface
    {
        $factory = new self(static::$driver);
        $create = $factory->createQuery($table_name);
        return $create;
    }

    public static function update(string $table_name, array $data): UpdateInterface
    {
        $factory = new self(static::$driver);
        $update = $factory->updateQuery($table_name, $data);
        return $update;
    }
}

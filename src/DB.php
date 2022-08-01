<?php

namespace CodesVault\WPqb;

use CodesVault\WPqb\Api\InsertInterface;
use CodesVault\WPqb\Api\SelectInterface;

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
        $insert = $factory->insertQuery($table_name, $data);
        return $insert;
    }
}

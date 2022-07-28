<?php

namespace CodesVault\WPqb;

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
}

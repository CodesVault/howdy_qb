<?php

namespace CodesVault\WPqb;

class DB extends QueryFactory
{
    private static $driver = 'pdo';

    public function setDriver($driver)
    {
        static::$driver = $driver;
    }

    public static function select(...$columns)
    {
        $factory = new self(static::$driver);
        $query = $factory->selectQuery();
        $query = $query->columns(...$columns);
        return $query;
    }
}

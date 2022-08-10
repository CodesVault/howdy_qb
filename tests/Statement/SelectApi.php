<?php

namespace CodesVault\WPqb\Tests\Statement;

use CodesVault\WPqb\Statement\Select;
use CodesVault\WPqb\SqlGenerator;
use PHPUnit\Framework\TestCase;

class SelectApi extends Select
{
    public function __construct($db)
    {
        $this->db = $db;
        $this->wpdb_object = new \stdClass;
        $this->wpdb_object->prefix = 'wp_';
    }

    public static function select($colmns)
    {
        $driver = new \stdClass;
        $select = new self($driver);
        $select->columns($colmns);
        return $select;
    }

    public function getSql()
    {
        $this->start();
        $this->setAlias();
        $this->setStartExpression();
        $query = SqlGenerator::select($this->sql);

        return $query;
    }
}

<?php

namespace CodesVault\WPqb;

use CodesVault\WPqb\Expression\Select;

class QueryFactory
{
    protected $db;

    public function __construct($driver = 'pdo')
    {
        if ( $driver === 'pdo' ) {
            $this->db = Connect::pdo();
        }
    }

    public function selectQuery(): Select
    {
        return new Select($this->db);
    }
}

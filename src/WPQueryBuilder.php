<?php

namespace CodesVault\WPqb;

use Latitude\QueryBuilder\QueryFactory;

class WPQueryBuilder extends QueryFactory
{
    public function select(...$columns): Select
    {
        $query = $this->engine->makeSelect();
        if (empty($columns) === false) {
            $query = $query->columns(...$columns);
        }
        return $query;
    }
}

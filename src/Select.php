<?php

namespace CodesVault\WPqb;

use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\identifyAll;

class Select extends SelectQuery
{
    public function from(...$tables): self
    {
        global $wpdb;
        $tbls = [];
        foreach ($tables as $table) {
            $tbls[] = $wpdb->prefix . $table;
        }
        $this->from = identifyAll($tbls);

        return $this;
    }
}

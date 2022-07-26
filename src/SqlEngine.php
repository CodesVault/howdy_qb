<?php

namespace CodesVault\WPqb;

use Latitude\QueryBuilder\Engine\MySqlEngine;

class SqlEngine extends MySqlEngine
{
    public function makeSelect(): Select
    {
        return new Select($this);
    }
}

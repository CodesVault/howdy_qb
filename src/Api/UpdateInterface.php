<?php

namespace CodesVault\WPqb\Api;

interface UpdateInterface
{
    function where($column, string $operator = null, string $value = null): self;

    function andWhere(string $column, string $operator = null, string $value = null): self;

    function orWhere(string $column, string $operator = null, string $value = null): self;

    function execute();
}

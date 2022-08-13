<?php

namespace CodesVault\Howdyqb\Api;

interface DeleteInterface
{
    function where($column, string $operator = null, string $value = null): self;

    function andWhere(string $column, string $operator = null, string $value = null): self;

    function orWhere(string $column, string $operator = null, string $value = null): self;

    function drop();

    function execute();
}

<?php

namespace CodesVault\Howdyqb\Api;

interface UpdateInterface
{
    public function where($column, string $operator = null, string $value = null): self;

    public function andWhere(string $column, string $operator = null, string $value = null): self;

    public function orWhere(string $column, string $operator = null, string $value = null): self;

    function getSql();

    public function execute();
}

<?php

namespace CodesVault\Howdyqb\Api;

interface DeleteInterface
{
    public function where($column, string $operator = null, string $value = null): self;

    public function andWhere(string $column, string $operator = null, string $value = null): self;

    public function orWhere(string $column, string $operator = null, string $value = null): self;

    public function drop();

    public function dropIfExists();

    public function getSql();

    public function execute();
}

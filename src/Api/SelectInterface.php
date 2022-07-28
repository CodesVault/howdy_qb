<?php

namespace CodesVault\WPqb\Api;

interface SelectInterface
{
    function distinct(): self;

    function columns(...$columns): self;

    function alias(string $name): self;

    function from(string $table_name): self;

    function where($column, ?string $operator, ?string $value): self;

    function andWhere($column, ?string $operator, ?string $value): self;

    function orWhere($column, ?string $operator, ?string $value): self;

    function whereNot(string $column, ?string $operator, ?string $value): self;

    function andNot(string $column, ?string $operator, ?string $value): self;

    function whereIn(string $column, ...$value): self;

    function orderBy($column, string $sort): self;

    function groupBy($column): self;

    function limit(int $count): self;

    function get(): mixed;
}

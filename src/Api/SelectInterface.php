<?php

namespace CodesVault\WPqb\Api;

interface SelectInterface
{
    function distinct(): self;

    function columns(...$columns): self;

    function alias(string $name): self;

    function from(string $table_name): self;

    /**
     * JOIN table(s) ON condition of
     * $col1 and $col2 columns
     *
     * @param string|array $table_name
     * @param string|null $col1
     * @param string|null $col2
     *
     * @return self
     */
    function join($table_name, string $col1, string $col2): self;

    /**
     * INNER JOIN table(s) ON condition of
     * $col1 and $col2 columns
     *
     * @param string|array $table_name
     * @param string|null $col1
     * @param string|null $col2
     *
     * @return self
     */
    function innerJoin($table_name, string $col1, string $col2): self;

    function where($column, ?string $operator = null, ?string $value = null): self;

    function andWhere(string $column, ?string $operator, ?string $value): self;

    function orWhere(string $column, ?string $operator, ?string $value): self;

    function whereNot(string $column, ?string $operator, ?string $value): self;

    function andNot(string $column, ?string $operator, ?string $value): self;

    function whereIn(string $column, ...$value): self;

    function orderBy($column, string $sort): self;

    function groupBy($column): self;

    function limit(int $count): self;

    function get(): mixed;
}

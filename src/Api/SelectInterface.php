<?php

namespace CodesVault\Howdyqb\Api;

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
     * @return CodesVault\Howdyqb\Api\SelectInterface
     */
    function join($table_name, string $col1 = null, string $col2 = null): self;

    /**
     * INNER JOIN table(s) ON condition of
     * $col1 and $col2 columns
     *
     * @param string|array $table_name
     * @param string|null $col1
     * @param string|null $col2
     *
     * @return CodesVault\Howdyqb\Api\SelectInterface
     */
    function innerJoin($table_name, string $col1 = null, string $col2 = null): self;

    /**
     * LEFT JOIN table(s) ON condition of
     * $col1 and $col2 columns
     *
     * @param string|array $table_name
     * @param string|null $col1
     * @param string|null $col2
     *
     * @return CodesVault\Howdyqb\Api\SelectInterface
     */
    function leftJoin($table_name, string $col1 = null, string $col2 = null): self;

    /**
     * RIGHT JOIN table(s) ON condition of
     * $col1 and $col2 columns
     *
     * @param string|array $table_name
     * @param string|null $col1
     * @param string|null $col2
     *
     * @return CodesVault\Howdyqb\Api\SelectInterface
     */
    function rightJoin($table_name, string $col1 = null, string $col2 = null): self;

    function where($column, ?string $operator = null, ?string $value = null): self;

    function andWhere(string $column, ?string $operator, ?string $value): self;

    function orWhere(string $column, ?string $operator, ?string $value): self;

    function whereNot(string $column, ?string $operator, ?string $value): self;

    function andNot(string $column, ?string $operator, ?string $value): self;

    function whereIn(string $column, ...$value): self;

    function orderBy($column, string $sort): self;

    function groupBy($column): self;

    function limit(int $count): self;

    function offset(int $count): self;

    function count(string $column, string $alias = ''): self;

    function raw(string $sql): self;

    function get();
}

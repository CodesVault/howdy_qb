<?php

namespace CodesVault\Howdyqb\Api;

interface SelectInterface
{
    public function distinct(): self;

    public function columns(...$columns): self;

    public function alias(string $name): self;

    public function from(string $table_name): self;

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
    public function join($table_name, string $col1 = null, string $col2 = null): self;

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
    public function innerJoin($table_name, string $col1 = null, string $col2 = null): self;

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
    public function leftJoin($table_name, string $col1 = null, string $col2 = null): self;

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
    public function rightJoin($table_name, string $col1 = null, string $col2 = null): self;

    public function where($column, ?string $operator = null, ?string $value = null): self;

    public function andWhere(string $column, ?string $operator, ?string $value): self;

    public function orWhere(string $column, ?string $operator, ?string $value): self;

    public function whereNot(string $column, ?string $operator, ?string $value): self;

    public function andNot(string $column, ?string $operator, ?string $value): self;

    public function whereIn(string $column, ...$value): self;

    public function orderBy($column, string $sort): self;

    public function groupBy($column): self;

    public function limit(int $count): self;

    public function offset(int $count): self;

    public function count(string $column, string $alias = ''): self;

    public function raw(string $sql): self;

    public function getSql();

    public function get();
}

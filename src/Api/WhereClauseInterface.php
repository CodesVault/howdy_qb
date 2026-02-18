<?php

namespace CodesVault\Howdyqb\Api;

interface WhereClauseInterface
{
	public function where($column, ?string $operator = null, $value = null): self;

	public function andWhere(string $column, ?string $operator, ?string $value): self;

	public function orWhere(string $column, ?string $operator, ?string $value): self;

	public function whereNot(string $column, ?string $operator, ?string $value): self;

	public function andNot(string $column, ?string $operator, ?string $value): self;

	public function andIn(string $column, ...$value): self;

	public function whereIn(string $column, ...$value): self;
}

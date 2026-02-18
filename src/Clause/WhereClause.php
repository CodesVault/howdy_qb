<?php

namespace CodesVault\Howdyqb\Clause;

use CodesVault\Howdyqb\Utilities;
use CodesVault\Howdyqb\Validation\IdentifierValidator;

trait WhereClause
{
	public function where($column, ?string $operator = null, $value = null): self
    {
        if (is_callable($column)) {
            call_user_func($column, $this);
            return $this;
        }

		$column = IdentifierValidator::validateColumnName($column);
		$operator = IdentifierValidator::validateOperator($operator);

		if (is_callable($value)) {
			$subQueryInstence = new SubQuery($this->db);
			call_user_func($value, $subQueryInstence);
			$subQuerySql = $subQueryInstence->getSql();

			$this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . " (" . $subQuerySql['query'] . ")";

			foreach ($subQuerySql['params'] as $param) {
				$this->params[] = $param;
			}

			return $this;
		}

        $this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, ?string $operator = null, $value = null): self
    {
		$column = IdentifierValidator::validateColumnName($column);
		$operator = IdentifierValidator::validateOperator($operator);

        $this->sql['andWhere'][] = 'AND ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, ?string $operator = null, $value = null): self
    {
		$column = IdentifierValidator::validateColumnName($column);
		$operator = IdentifierValidator::validateOperator($operator);

        $this->sql['orWhere'][] = 'OR ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function whereNot(string $column, ?string $operator = null, $value = null): self
    {
		$column = IdentifierValidator::validateColumnName($column);
		$operator = IdentifierValidator::validateOperator($operator);

        $this->sql['whereNot'][] = 'WHERE NOT ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andNot(string $column, ?string $operator = null, $value = null): self
    {
		$column = IdentifierValidator::validateColumnName($column);
		$operator = IdentifierValidator::validateOperator($operator);

        $this->sql['andNot'][] = 'AND NOT ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

	public function andIn(string $column, ...$value): self
	{
		$column = IdentifierValidator::validateColumnName($column);

		if (is_callable($value)) {
			$subQueryInstence = new SubQuery($this->db);
			call_user_func($value, $subQueryInstence);
			$subQuerySql = $subQueryInstence->getSql();

			$this->sql['andIn'] = "AND " . $column . " IN (" . $subQuerySql['query'] . ")";

			foreach ($subQuerySql['params'] as $param) {
				$this->params[] = $param;
			}

			return $this;
		}

		$list = implode(', ', array_map(function($item) {
			$this->params[] = $item;
			return Utilities::get_placeholder($this->db, $item);
		}, $value));

        $this->sql['andIn'][] = 'AND ' . $column . " IN ($list)";

		return $this;
	}

    public function whereIn(string $column, ...$value): self
    {
		$column = IdentifierValidator::validateColumnName($column);

		if (count($value) === 1 && is_callable($value[0])) {
			$subQueryInstence = new SubQuery($this->db);
			call_user_func($value[0], $subQueryInstence);
			$subQuerySql = $subQueryInstence->getSql();

			$this->sql['whereIn'][] = 'WHERE ' . $column . " IN (" . $subQuerySql['query'] . ")";

			foreach ($subQuerySql['params'] as $param) {
				$this->params[] = $param;
			}

			return $this;
		}

		$list = implode(', ', array_map(function($item) {
			$this->params[] = $item;
			return Utilities::get_placeholder($this->db, $item);
		}, $value));

        $this->sql['whereIn'][] = 'WHERE ' . $column . " IN (" . $list . ")";
        return $this;
    }
}

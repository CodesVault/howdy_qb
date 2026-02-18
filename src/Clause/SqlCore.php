<?php

namespace CodesVault\Howdyqb\Clause;

use CodesVault\Howdyqb\Utilities;
use CodesVault\Howdyqb\Validation\IdentifierValidator;

trait SqlCore
{
	public function columns(...$columns): self
    {
		if (empty($columns)) {
			return $this;
		}

		$this->sql['columns'] = '';
		foreach ($columns as $column) {
			if (is_callable($column)) {
				$subQueryInstence = new SubQuery($this->db);
				call_user_func($column, $subQueryInstence);

				$alias = $subQueryInstence->columnAlias();
				$subQuerySql = $subQueryInstence->getSql();
				$this->sql['columns'] .= "(" . $subQuerySql['query'] . ") $alias,";

				foreach ($subQuerySql['params'] as $param) {
					$this->params[] = $param;
				}
				continue;
			}
			$this->sql['columns'] .= IdentifierValidator::validateColumnName($column) . ", ";
		}

		$this->sql['columns'] = rtrim($this->sql['columns'], ", ");

        return $this;
    }

	public function distinct(): self
    {
        $this->sql['start']['distinct'] = 'DISTINCT';
        return $this;
    }

    public function alias(string $name): self
    {
        $this->sql['alias'] = 'AS ' . IdentifierValidator::validateColumnName($name);
        return $this;
    }

	protected function setAlias()
    {
        if (! isset($this->sql['alias'])) return;

        $this->sql['table_name'] .= ' ' . $this->sql['alias'];
        unset($this->sql['alias']);
    }

    public function from(string $table_name): self
    {
		$from = explode(' ', $table_name);
		$tableName = '';
		$alias = '';

		if (count($from) > 1) {
			$tableName = IdentifierValidator::validateTableName(Utilities::get_db_configs()->prefix . $from[0]);
			$alias = IdentifierValidator::validateTableName($from[1]);
		} else {
			$tableName = IdentifierValidator::validateTableName(Utilities::get_db_configs()->prefix . $from[0]);
		}

        $this->sql['table_name'] = trim('FROM ' . $tableName . ' ' . $alias);
        return $this;
    }

    public function orderBy(array|string $column, string $sort_type = 'ASC'): self
    {
		if (is_array($column)) {
			foreach ($column as $col => $shortType) {
				$this->sql['orderBy'] = $this->sql['orderBy'] ?? '';
				$this->sql['orderBy'] .= IdentifierValidator::validateColumnName($col) . ' ' . $shortType . ', ';
			}
			$this->sql['orderBy'] = 'ORDER BY ' . rtrim($this->sql['orderBy'], ', ');
			return $this;
		}

        $this->sql['orderBy'] = 'ORDER BY ' . IdentifierValidator::validateColumnName($column) . ' ' . $sort_type;
        return $this;
    }

    public function groupBy($column): self
    {
        $col = is_array( $column ) ? implode( ', ', $column ) : $column;
		if (is_array($column)) {
			foreach ($column as $col) {
				$this->sql['groupBy'] = $this->sql['groupBy'] ?? '';
				$this->sql['groupBy'] .= IdentifierValidator::validateColumnName($col) . ', ';
			}
			$this->sql['groupBy'] = 'GROUP BY ' . rtrim($this->sql['groupBy'], ', ');
			return $this;
		}

        $this->sql['groupBy'] = 'GROUP BY ' . $col;
        return $this;
    }

    public function limit(int $count): self
    {
        $this->sql['limit'] = 'LIMIT ' . $count;
        return $this;
    }

    public function offset(int $count): self
    {
        $this->sql['offset'] = 'OFFSET ' . $count;
        return $this;
    }

    public function count(string $column, string $alias = ''): self
    {
		$column = IdentifierValidator::validateColumnName($column);
		$alias = IdentifierValidator::validateColumnName($alias);
        $alias = $alias ? ' ' . $alias : '';
        $this->sql['start']['count'] = 'COUNT(' . $column . ')' . $alias;
        return $this;
    }

    public function raw(string $sql): self
    {
        $this->sql['raw_'. $this->row_count++] = $sql;
        return $this;
    }

	public function avg(string $column, string $alias = ''): self
	{
		$columnName = IdentifierValidator::validateColumnName($column);
		$aliasStr = $alias ? ' AS ' . IdentifierValidator::validateColumnName($alias) : '';
		$expression = "AVG($columnName)" . $aliasStr;

		if (!empty($this->sql['columns'])) {
			$this->sql['columns'] .= ', ' . $expression;
		} else {
			$this->sql['columns'] = $expression;
		}

		return $this;
	}

	protected function setStartExpression()
    {
        $sql = '';
        if (isset($this->sql['start']['select'])) {
            $sql .= 'SELECT ';
        }
        if (isset($this->sql['start']['distinct'])) {
            $sql .= 'DISTINCT ';
        }
        if (isset($this->sql['columns'])) {
            $sql .= $this->sql['columns'];
			$sql .= isset($this->sql['start']['count']) ? ', ' : '';
            unset($this->sql['columns']);
        }
        if (isset($this->sql['start']['count'])) {
            $sql .= $this->sql['start']['count'];
        }
        return $this->sql['start'] = $sql;
    }
}

<?php

namespace CodesVault\Howdyqb\Expression;

use CodesVault\Howdyqb\Utilities;

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
			$this->sql['columns'] .= $column . ", ";
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
        $this->sql['alias'] = 'AS ' . $name;
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
        $this->sql['table_name'] = 'FROM ' . Utilities::get_db_configs()->prefix . $table_name;
        return $this;
    }

    public function where($column, ?string $operator = null, $value = null): self
    {
        if (is_callable($column)) {
            call_user_func($column, $this);
            return $this;
        }

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
        $this->sql['andWhere'][] = 'AND ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, ?string $operator = null, $value = null): self
    {
        $this->sql['orWhere'][] = 'OR ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function whereNot(string $column, ?string $operator = null, $value = null): self
    {
        $this->sql['whereNot'][] = 'WHERE NOT ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andNot(string $column, ?string $operator = null, $value = null): self
    {
        $this->sql['andNot'][] = 'AND NOT ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

	public function andIn(string $column, ...$value): self
	{
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

    public function orderBy($column, string $sort_type): self
    {
        $col = is_array( $column ) ? implode( ', ', $column ) : $column;
        $this->sql['orderBy'] = 'ORDER BY ' . $col . ' ' . $sort_type;
        return $this;
    }

    public function groupBy($column): self
    {
        $col = is_array( $column ) ? implode( ', ', $column ) : $column;
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
        $alias = $alias ? ' ' . $alias : '';
        $this->sql['start']['count'] = 'COUNT(' . $column . ')' . $alias;
        return $this;
    }

	private function setJoin($table_name, ?string $col1 = null, ?string $col2 = null, string $joinType = 'JOIN'): self
    {
        $table_names = [];
        if (is_array($table_name)) {
            foreach ($table_name as $table) {
                $table_names[] = Utilities::get_db_configs()->prefix . $table;
            }
        } else {
            $table_names[] = Utilities::get_db_configs()->prefix . $table_name;
        }

        $table = '';
        if (count($table_names) > 1) {
            $table = '(' . implode(',', $table_names) . ')';
        } else {
            $table = $table_names[0];
        }

        $this->sql['join'] = $joinType . ' ' . $table;
        if ($col1 && $col2) {
            $this->sql['join'] .= ' ON ' . $col1 . ' = ' . $col2;
        }
        return $this;
    }

    public function join($table_name, ?string $col1 = null, ?string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2);
    }

    public function innerJoin($table_name, ?string $col1 = null, ?string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2, 'INNER JOIN');
    }

    public function leftJoin($table_name, ?string $col1 = null, ?string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2, 'LEFT JOIN');
    }

    public function rightJoin($table_name, ?string $col1 = null, ?string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2, 'RIGHT JOIN');
    }

    public function raw(string $sql): self
    {
        $this->sql['raw_'. $this->row_count++] = $sql;
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

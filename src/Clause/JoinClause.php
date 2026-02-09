<?php

namespace CodesVault\Howdyqb\Clause;

use CodesVault\Howdyqb\Validation\IdentifierValidator;

trait JoinClause
{
	private function setJoin($table_name, ?string $col1 = null, ?string $col2 = null, string $joinType = 'JOIN'): self
    {
        $table_names = [];
        if (is_array($table_name)) {
            foreach ($table_name as $table) {
				$table_names[] = IdentifierValidator::validateTableNameWithAlias($table);
            }
        } else {
			$table_names[] = IdentifierValidator::validateTableNameWithAlias($table_name);
        }

		$table = $table_names[0];
        if (count($table_names) > 1) {
            $table = '(' . implode(',', $table_names) . ')';
        }

        $this->sql['join'] = $joinType . ' ' . $table;
        if ($col1 && $col2) {
			$col1 = IdentifierValidator::validateColumnName($col1);
			$col2 = IdentifierValidator::validateColumnName($col2);
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
}

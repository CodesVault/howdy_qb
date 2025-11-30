<?php

namespace CodesVault\Howdyqb;

class SqlGenerator
{
    public static function select(array $sql)
    {
        $query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
        }
        foreach ($sql as $key => $value) {
            if ($key == 'start') continue;
			if (is_array($value)) {
				$value = trim(implode(' ', $value));
			}
            $query .= $value . ' ';
        }
        return trim($query);
    }

    public static function insert(array $sql, array $select_sql = [])
    {
        $query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
            unset($sql['start']);
        }
        foreach ($sql as $value) {
            $query .= $value . ' ';
        }

		if (! empty($select_sql['start'])) {
			$query = trim($query) . ' ';
			$query .= self::select($select_sql);
		}

        return trim($query);
    }

    public static function create(array $sql)
    {
        $query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
        }
        if (isset($sql['table_name'])) {
            $query .= $sql['table_name'] . ' ';
        }

        $query .= '(';
        foreach ($sql as $ex => $expression) {
            if ($ex == 'start' || $ex == 'table_name' || $ex == 'unique') continue;

            if (is_array($expression)) {
                foreach ($expression as $name => $column) {
                    $expression[$name] = '`'. $name . '` ' . implode(' ', $column);
                }
                $query .= implode(', ', $expression);

				continue;
            }

			$query .= ', ' . $expression . '';
        }

		if (! empty($sql['unique']) && is_array($sql['unique'])) {
			$query .= ', UNIQUE (' . implode(',', $sql['unique']) . ')';
		}

        $query .= ')';

        return trim($query);
    }

	public static function alter(array $sql)
	{
		$query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
        }
		foreach ($sql as $key => $value) {
            if ($key == 'start') continue;
            $query .= $value . ' ';
        }
        $query = trim($query);
		return $query . ";";
	}

    public static function update(array $sql)
    {
        $query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
        }
        if (isset($sql['set_columns'])) {
            $query .= $sql['set_columns'] . ' ';
        }
        foreach ($sql as $key => $value) {
            if ($key == 'start' || $key == 'set_columns') continue;
			if (is_array($value)) {
				$value = trim(implode(' ', $value));
			}
            $query .= $value . ' ';
        }
        return trim($query);
    }

    public static function delete(array $sql)
    {
        $query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
        }
        if (isset($sql['drop'])) {
            $query = $sql['drop'] . ' ';
            return trim($query);
        }
        foreach ($sql as $key => $value) {
            if ($key == 'start') continue;
			if (is_array($value)) {
				$value = trim(implode(' ', $value));
			}
            $query .= $value . ' ';
        }
        return trim($query);
    }
}

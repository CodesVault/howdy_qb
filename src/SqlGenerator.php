<?php

namespace CodesVault\WPqb;

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
            $query .= $value . ' ';
        }
        return $query;
    }

    public static function insert(array $sql)
    {
        $query = '';
        if (isset($sql['start'])) {
            $query .= $sql['start'] . ' ';
            unset($sql['start']);
        }
        foreach ($sql as $value) {
            $query .= $value . ' ';
        }
        return $query;
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
            if ( $ex == 'start' || $ex == 'table_name' ) continue;

            if (is_array($expression)) {
                foreach ($expression as $name => $column) {
                    $expression[$name] = $name . ' ' . implode(' ', $column);
                }
                $query .= implode(', ', $expression);
            } else {
                $query .= ', ' . $expression . ' ';
            }
        }
        $query .= ')';

        return $query;
    }
}

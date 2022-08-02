<?php

namespace CodesVault\WPqb;

class SqlGenerator
{
    protected static $sql;

    public static function run($sql)
    {
        static::$sql = $sql;

        $query = '';
        if ( isset( $sql['start'] ) ) {
            static::setDistinct();
            $query .= static::$sql['start'] . ' ';
            unset( static::$sql['start'] );
        }
        foreach ( static::$sql as $value ) {
            if ( isset( static::$sql['alias'] ) ) {
                static::setAlias();
            }
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

    private static function setAlias()
    {
        static::$sql['table_name'] .= ' ' . static::$sql['alias'];
        unset( static::$sql['alias'] );
    }

    private static function setDistinct()
    {
        if ( isset( static::$sql['distinct'] ) ) {
            static::$sql['start'] = static::$sql['start'] . ' ' . static::$sql['distinct'];
            unset( static::$sql['distinct'] );
        }
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
        // dump($query);

        return $query;
    }
}

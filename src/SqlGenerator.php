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
}

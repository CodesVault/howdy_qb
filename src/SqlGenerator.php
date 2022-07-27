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
            $query .= $value . ' ';
        }
        return $query;
    }

    private static function setDistinct()
    {
        if ( isset( static::$sql['distinct'] ) ) {
            static::$sql['start'] = static::$sql['start'] . ' ' . static::$sql['distinct'];
            unset( static::$sql['distinct'] );
        }
    }
}

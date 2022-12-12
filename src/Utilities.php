<?php

namespace CodesVault\Howdyqb;

class Utilities
{
    public static function throughException($exception)
    {
        printf("<div class='howdy-qb'>");
        $error_msg = sprintf(
            "<strong style='color: #d60202;'>%s</strong>  <strong style='color: red;'>%s</strong>",
            'ERROR Message:',
            $exception->getMessage()
        );
        printf($error_msg);
        printf("<br /><strong>In %s %s</strong><br /><br />", $exception->getFile(), $exception->getLine());
        printf(
            "<div style='max-height: 300px; overflow: scroll;'><strong>Trace:</strong> <pre>%s</pre> </div><br />",
            print_r($exception->getTrace(), true)
        );
        printf("</div>");

        throw new \Exception($exception->getMessage());
    }

    public static function get_placeholder()
    {
        $driver = QueryFactory::getDriver();
        if ('wpdb' === $driver) {
            return '%s';
        }
        return "?";
    }
}

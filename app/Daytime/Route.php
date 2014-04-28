<?php namespace Daytime;

use Macaw;

class Route extends Macaw {

    /**
     * Defines a route w/ callback and method
     * TODO: Fix this, just made fast to get it working on the testing server
     */
    public static function __callstatic($method, $params)
    {
        $baseUri = dirname($_SERVER['PHP_SELF']);
        if ($baseUri == '/')
            $baseUri = '';

        $uri = $baseUri.$params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }
} 

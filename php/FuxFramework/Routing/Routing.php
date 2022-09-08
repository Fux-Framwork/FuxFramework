<?php

namespace Fux\Routing;

use Fux\Request;
use Fux\Router;

class Routing
{

    private static $router;

    public static function router(){
        if (!self::$router) self::$router = new Router(new Request());
        return self::$router;
    }

}
<?php

namespace Fux\Routing;

use Fux\Middleware\DefaultCsrfProtectionMiddleware;
use Fux\Request;
use Fux\Router;

class Routing
{

    private static $router;

    public static function router()
    {
        if (!self::$router) {
            if (ENABLE_DEFAULT_CSRF_MIDDLEWARE) {
                (new Router(new Request()))->withMiddleware([new DefaultCsrfProtectionMiddleware()], function ($router) {
                    self::$router = $router;
                });
            } else {
                self::$router = new Router(new Request());
            }
        }
        return self::$router;
    }

}
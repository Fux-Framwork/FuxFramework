<?php

use Fux\FuxResponse;
use Fux\Request;


\Fux\Routing\Routing::router()->withMiddleware([new ExampleMiddleware()], function ($router) {
    $router->get('/', function (Request $request) {
        return view('myExampleView');
    });

    $router->post('/send-form', function (Request $request) use($router) {
        print_r_pre($router->getMiddlewares());
        return \App\Controllers\TestController::sendForm($request);
    })->middleware(new ExampleMiddleware());
});



<?php

use Fux\FuxResponse;
use Fux\Request;


\Fux\Routing\Routing::router()->get('/', function (Request $request) {
    return view('myExampleView');
});

\Fux\Routing\Routing::router()->post('/send-form', function (Request $request) {
    return \App\Controllers\TestController::sendForm($request);
});

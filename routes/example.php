<?php

use Fux\FuxResponse;
use Fux\Request;

\Fux\Routing\Routing::router()->get('/', function (Request $request) {
    return "Welcome in FuxFramework!";
});

\Fux\Routing\Routing::router()->get('/home', function (Request $request) {
    return TestController::myTestMethod($request);
});

\Fux\Routing\Routing::router()->get('/error', function () {
    return new FuxResponse("ERROR", "This is an error!", null, true);
});

\Fux\Routing\Routing::router()->get('/success', function () {
    return new FuxResponse("OK", "This is custom success page!", [
        "forwardLink" => "https://google.com",
        "forwardLinkText" => "Go to Google homepage"
    ], true);
});

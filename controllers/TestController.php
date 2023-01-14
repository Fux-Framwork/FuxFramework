<?php


class TestController
{

    public static function myTestMethod(\Fux\Request $request)
    {
        $params = $request->getQueryStringParams();
        return view("myExampleView", ["myViewParameter" => "HelloWorld", "params" => $params]);
    }

}

<?php

namespace App\Controllers;

use Fux\Request;

class TestController
{

    public static function sendForm(Request $request)
    {
        print_r_pre($request->getBody());
        return "CURRENT CSRF = ".csrf_token()."<br>";
    }

}

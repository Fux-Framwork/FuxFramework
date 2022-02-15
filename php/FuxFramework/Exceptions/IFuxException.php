<?php

namespace Fux\Exceptions;

use Fux\FuxResponse;
use Fux\Request;

interface IFuxException
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report();

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @param \Exception $exception
     *
     * @return string | FuxResponse
     */
    public function render(\Fux\Request $request, \Exception $exception);

}
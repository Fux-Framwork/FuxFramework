<?php

namespace Fux\Exceptions;

use Fux\FuxResponse;
use Fux\Request;
use Throwable;

class FuxException extends \Exception implements IFuxException
{

    protected $canBePretty = false;

    public function __construct($canBePretty = false, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->canBePretty = $canBePretty;
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @param \Exception $exception
     *
     * @return string | FuxResponse
     */
    public function render($request, $exception)
    {
        return new FuxResponse("ERROR", $exception->getMessage(), null, $this->canBePretty);
    }

}
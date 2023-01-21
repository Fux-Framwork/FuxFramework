<?php

namespace Fux;

use Fux\Exceptions\FuxException;
use Fux\Security\Encryption\Encrypter;

class DefaultCsrfProtectionMiddleware extends FuxMiddleware
{

    /**
     * @throws FuxException
     */
    public function handle()
    {

        //Check if current request match one of the excluded routes the middleware is skipped
        foreach(CSRF_EXCUDED_ROUTES as $route){
            if ($this->request->matchRoute($route)) $this->resolve();
        }

        $token = $this->getRequestToken();
        $realToken = csrf_token();

        if (is_string($realToken) && is_string($token) && hash_equals($realToken, $token)) {
            if (ADD_XSRF_TOKEN_COOKIE) {
                $this->addCsrfTokenCookie();
            }
            return $this->resolve();
        }

        throw new FuxException(false, "CSRF token mismatch.");
    }

    protected function getRequestToken()
    {
        //Check if the post request has a _token property
        $body = $this->request->getBody();
        if (isset($body['_token'])) return $body['_token'];

        $headers = $this->request->headers();
        //Check X-CSRF-TOKEN in request header
        if (isset($headers['X-CSRF-TOKEN'])) return $headers['X-CSRF-TOKEN'];

        //Check the encrypted X-XSRF-TOKEN
        if (isset($headers['X-XSRF-TOKEN'])) return Encrypter::decrypt($headers['X-XSRF-TOKEN']);

        return null;
    }

    protected function addCsrfTokenCookie()
    {
        setcookie('XSRF-TOKEN', csrf_token(), XSRF_TOKEN_COOKIE_LIFETIME);
    }
}

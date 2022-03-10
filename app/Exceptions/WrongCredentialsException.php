<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WrongCredentialsException extends Exception
{
    protected $message = 'Incorrect login credentials';

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request) {
        return new Response("login failed", 401);
    }
}
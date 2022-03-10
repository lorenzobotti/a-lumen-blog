<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotLoggedException extends Exception
{
    protected $message = 'User is not logged in';

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request) {
        return new Response("you're not logged in", 409);
    }
}
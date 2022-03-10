<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class ServerErrorException extends Exception
{
    protected $message = 'Server error';

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request) {
        return new Response("server error", 500);
    }
}
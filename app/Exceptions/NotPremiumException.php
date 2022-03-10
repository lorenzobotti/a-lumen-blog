<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotPremiumException extends Exception
{
    protected $message = 'User is poor';

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request) {
        return new Response("you're poor", 401);
    }
}
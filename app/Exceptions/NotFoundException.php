<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotFoundException extends Exception
{
    protected $message = 'Resource not found';

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request) {
        return new Response('not found', 404);
    }
}
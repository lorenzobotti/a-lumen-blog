<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotYoursException extends Exception
{
    protected $message = 'User is not the original poster';

    /**
     * @param Request $request
     * @return Response
     */
    public function render(Request $request) {
        return new Response("this is not your post/comment", 401);
    }
}
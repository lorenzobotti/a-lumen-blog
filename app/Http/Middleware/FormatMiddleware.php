<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class FormatMiddleware
{
    /**
     * Se il valore ritornato del controller è un oggetto response con
     * status code != 2xx, formatta un messaggio di errore, se no lo
     * formatta in un campo `data`
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $res = $next($request);
        if ($res instanceof Response) {
            if ($res->status() < 200 || $res->status() >= 300) {
                $res->setContent(json_encode([
                    'error' => $res->getContent(),
                ]));

                return $res;
            } else {
                return new Response(json_encode([
                    'data' => $res->original,
                ]), 200);
            }
        } else {
            return new Response(json_encode([
                'data' => $res,
            ]), 200);
        }
    }
}
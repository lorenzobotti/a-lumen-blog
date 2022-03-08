<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class QueriesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // TODO: far riconoscere all'app da sola se è in local o prod (sarà una variabile d'ambiente?)
        $staging = true;

        DB::enableQueryLog();
        $res = $next($request);
        $queries = DB::getQueryLog();

        // per preservare lo status code originale
        $status = 200;
        if ($res instanceof Response) {
            $status = $res->status();
        }

        if ($staging) {
            return new Response([
                'response' => $res,
                'queries' => $queries,
            ], $status);
        } else {
            return $res;
        }
    }
}

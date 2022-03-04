<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        DB::enableQueryLog();
        $res = $next($request);
        $queries = DB::getQueryLog();

        return [
            'response' => $res,
            'queries' => $queries,
        ];
    }
}

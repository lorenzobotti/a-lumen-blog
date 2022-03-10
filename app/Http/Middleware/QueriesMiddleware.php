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
        $staging = getenv('APP_ENV') === 'local';
        // $staging = false;

        if ($staging) {
            DB::enableQueryLog();
        }

        $res = $next($request);

        if (!$staging) {
            return $res;
        }
        $queries = DB::getQueryLog();

        // per preservare lo status code originale
        $status = 200;
        if ($res instanceof Response) {
            $status = $res->status();
        }

        // è convoluto ma il succo è che prova a interpretare la risposta
        // come json e se non riesce manda l'originale come stringa
        $decodedContent = json_decode($res->content());
        $content = $decodedContent;
        if (!$content) {
            $content = $res->content();
        }

        return new Response([
            'response' => $content,
            'queries' => $queries,
        ], $status);
    }
}

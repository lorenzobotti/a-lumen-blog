<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BannedMiddleware
{
    /**
     * Autorizza la richiesta solo se l'utente è loggato e non bannato.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->banned_at) {
            return new Response('banned lmao', 403);
        }

        return $next($request);
    }
}

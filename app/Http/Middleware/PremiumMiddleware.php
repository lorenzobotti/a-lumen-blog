<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PremiumMiddleware
{
    /**
     * Rejects the request if the user is not premium
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user->subscription !== 'premium') {
            return new Response('', 403);
        }

        return $next($request);
    }
}
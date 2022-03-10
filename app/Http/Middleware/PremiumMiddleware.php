<?php

namespace App\Http\Middleware;

use App\Exceptions\NotPremiumException;
use App\Models\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PremiumMiddleware
{
    /**
     * Rejects the request if the user is not premium or a moderator
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!in_array($user->subscription, ['premium', 'mod'], true)) {
            throw new NotPremiumException();
        }

        return $next($request);
    }
}
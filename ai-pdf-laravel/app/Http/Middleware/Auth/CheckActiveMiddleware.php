<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class CheckActiveMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = data_get($request, 'user');

        if (!$user || !$user->is_active) {
            return response()->error('Your account is not activated. Please verify your email first.', 403);
        }

        return $next($request);
    }
}

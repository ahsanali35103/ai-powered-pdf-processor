<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckUserExistMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $email = strtolower(trim($request->input('email')));

        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->error('User already exists with this email.', 409);
        }

        return $next($request);
    }
}

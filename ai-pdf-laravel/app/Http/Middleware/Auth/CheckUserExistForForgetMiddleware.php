<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckUserExistForForgetMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $email = strtolower(trim($request->input('email')));

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->error('No account found with this email address.');
        }

        if (!$user->is_active) {
            return response()->error('Please verify your email first before resetting password.', 403);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}

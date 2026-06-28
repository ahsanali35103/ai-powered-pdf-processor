<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CheckCredentialMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->error('Invalid email or password.', 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}

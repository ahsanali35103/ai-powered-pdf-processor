<?php

namespace App\Http\Middleware\Auth;

use App\Models\SessionToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckVerifySignupMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->error('Verification code is required.', 422);
        }

        // Find valid signup verification token
        $tokenRecord = SessionToken::findValidToken($token, 'signup_verification_token');

        if (!$tokenRecord) {
            return response()->error('Invalid or expired verification token.', 422);
        }

        // Find the user through the token record
        $user = User::find($tokenRecord->user_id);

        if (!$user) {
            return response()->error('User account not found.', 404);
        }

        if ($user->is_active) {
            return response()->error('This account is already verified.', 409);
        }

        $request->merge([
            'verified_user' => $user,
            'token_record'  => $tokenRecord,
        ]);

        return $next($request);
    }
}

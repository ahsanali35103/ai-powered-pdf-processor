<?php

namespace App\Http\Middleware\Auth;

use App\Models\SessionToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckTokenMiddleware
{
    public function handle(Request $request, Closure $next, string $type = null)
    {
        $token = $request->header('Authorization') ?? $request->input('token');

        if (!$token) {
            return response()->unauthorized('Access token is required.');
        }

        // Custom token validation - check if token exists in session_tokens
        $query = SessionToken::where('token', $token);

        // If a type is specified (e.g. forgot_password_token), filter by it
        if ($type) {
            $query->where('type', $type);
        }

        $sessionToken = $query->first();

        if (!$sessionToken) {
            $errorMsg = ($type === 'forgot_password_token') 
                ? 'Invalid or expired password reset token.' 
                : 'Invalid or expired access token.';
            return response()->unauthorized($errorMsg);
        }

        // Check if token is expired
        if ($sessionToken->expires_at && now()->greaterThan($sessionToken->expires_at)) {
            $errorMsg = ($type === 'forgot_password_token') 
                ? 'Invalid or expired password reset token.' 
                : 'Token has expired.';
            return response()->unauthorized($errorMsg);
        }

        // Get user associated with token
        $user = User::find($sessionToken->user_id);

        if (!$user) {
            return response()->unauthorized('User not found.');
        }

        $request->merge([
            'user'         => $user,
            'token_record' => $sessionToken,
        ]);

        return $next($request);
    }
}

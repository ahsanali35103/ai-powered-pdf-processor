<?php

namespace App\Http\Middleware;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\VerifySignupRequest;
use Closure;
use Illuminate\Http\Request;

class CheckValidationMiddleware
{
    public function handle(Request $request, Closure $next, string $validation_type)
    {
        if ($validation_type === 'logout_request') {
            $request->validate(app(LogoutRequest::class)->rules());
        }
        if ($validation_type === 'signup_request') {
            $request->validate(app(SignupRequest::class)->rules());
        }
        if ($validation_type === 'login_request') {
            $request->validate(app(LoginRequest::class)->rules());
        }
        if ($validation_type === 'verify_signup_request') {
            $request->validate(app(VerifySignupRequest::class)->rules());
        }
        if ($validation_type === 'forgot_password_request') {
            $request->validate(app(ForgotPasswordRequest::class)->rules());
        }
        if ($validation_type === 'reset_password_request') {
            $request->validate(app(ResetPasswordRequest::class)->rules());
        }
        
        return $next($request);
    }
}

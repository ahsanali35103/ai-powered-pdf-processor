<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
| Base URL: /api/auth/*
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    // POST /api/auth/signup
    Route::post('signup', [AuthController::class, 'signup'])
        ->middleware([
            'check.validation:signup_request',
            'check.user.exists',
        ]);

    // POST /api/auth/verify-signup
    Route::post('verify-signup', [AuthController::class, 'verifySignup'])
        ->middleware([
            'check.validation:verify_signup_request',
            'check.verify.signup',
        ]);

    // POST /api/auth/login
    Route::post('login', [AuthController::class, 'login'])
        ->middleware([
            'check.validation:login_request',
            'check.credentials',
            'check.active',
        ]);

    // POST /api/auth/logout
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware([
            'check.token',
        ]);

    // POST /api/auth/forgot-password
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware([
            'check.validation:forgot_password_request',
            'check.user.exists.forgot',
        ]);

    // POST /api/auth/reset-password
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->middleware([
            'check.validation:reset_password_request',
            'check.token:forgot_password_token',
        ]);

});
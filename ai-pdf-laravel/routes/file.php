<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\FcmTokenController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| File Service Routes
| Base URL: /api/file/*
|--------------------------------------------------------------------------
*/

Route::prefix('file')->group(function () {

    // POST /api/file/upload
    Route::post('/upload', [FileController::class, 'upload'])
        ->middleware('check.token');

    // DELETE /api/file/delete - Token authentication + Request validation
    Route::delete('/delete', [FileController::class, 'delete'])
        ->middleware('check.token');

    // FCM Token Management
    Route::post('/fcm/token', [FcmTokenController::class, 'create'])
        ->middleware('check.token');

});

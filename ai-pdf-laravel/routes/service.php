<?php

use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Service Routes (OCR, Summarization, Translation)
| Base URL: /api/service/*
|--------------------------------------------------------------------------
*/

Route::prefix('service')->group(function () {

    // POST /api/service/create
    Route::post('/create', [ServiceController::class, 'create'])
        ->middleware(['check.token', 'service.create']);

    Route::get('/languages', [ServiceController::class, 'languages']);

    // GET /api/service/read
    Route::get('/read', [ServiceController::class, 'read'])
        ->middleware(['check.token', 'service.read']);

    // GET /api/service/test-error - Test webhook notification
    Route::get('/test-error', function () {
        // This will trigger a 500 server error which should notify Whistle
        throw new \Exception('Test error for Whistle webhook notification - ' . date('Y-m-d H:i:s'));
    });

    // GET /api/service/test-whistle-direct - Direct test of Whistle webhook
    Route::get('/test-whistle-direct', function () {
        try {
            $response = \Illuminate\Support\Facades\Http::post('https://app.whistleit.io/api/webhooks/69f219662ba7dd8c7a0f9a97', [
                'test' => true,
                'message' => 'Direct test of Whistle webhook',
                'timestamp' => now()->toISOString(),
                'service' => 'AI-PDF-Processor Test'
            ]);
            
            return response()->json([
                'success' => true,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // GET /api/service/list
    Route::get('/list', [ServiceController::class, 'list'])
        ->middleware(['check.token', 'service.list']);

});

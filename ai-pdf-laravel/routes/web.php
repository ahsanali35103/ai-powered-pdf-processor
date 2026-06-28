<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Only the welcome page lives here. All API routes are in routes/api.php.
*/

Route::get('/', function () {
    return view('welcome');
});

// Temporary route to serve the FCM test page
Route::get('/fcm-test', function () {
    return response()->file(public_path('firebase-test.html'));
});

// Route to serve Service Worker (needed for FCM background messaging)
Route::get('/firebase-messaging-sw.js', function () {
    return response()->file(public_path('firebase-messaging-sw.js'), [
        'Content-Type' => 'application/javascript'
    ]);
});






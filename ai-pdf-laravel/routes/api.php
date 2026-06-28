<?php

use Illuminate\Support\Facades\Route;

// File Management Routes
Route::prefix('file')->group(function () {
    require __DIR__.'/file.php';
});

// Service Management Routes (OCR, Summarization, Translation)
Route::prefix('service')->group(function () {
    require __DIR__.'/service.php';
});







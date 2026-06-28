<?php

namespace App\Http\Middleware\Service;

use Closure;
use Illuminate\Http\Request;
use App\Models\File;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service Create Middleware
 */
class ServiceCreateMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user owns file
        $file = File::find($request->input('file_id'));
        if (!$file || $file->user_id !== $request->user->id) {
            return response()->forbidden('You do not have permission to process this file');
        }
        
        return $next($request);
    }
}
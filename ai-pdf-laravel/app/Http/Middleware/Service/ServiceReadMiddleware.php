<?php

namespace App\Http\Middleware\Service;

use Closure;
use Illuminate\Http\Request;
use App\Models\Service;
use Symfony\Component\HttpFoundation\Response;

class ServiceReadMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user owns service
        $service = Service::find($request->input('service_id'));
        if (!$service || $service->user_id !== $request->user->id) {
            return response()->forbidden('You do not have permission to read this service');
        }
        
        return $next($request);
    }
}

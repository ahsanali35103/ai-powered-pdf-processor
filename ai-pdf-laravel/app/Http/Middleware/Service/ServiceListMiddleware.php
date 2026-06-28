<?php

namespace App\Http\Middleware\Service;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceListMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // GET request - check for any actual user-provided query parameters
        $query = $request->getQueryString();
        
        // If there's any query string, reject it
        if ($query && !empty($query)) {
            return response()->error('No query parameters allowed. Only authentication token is required.', 422);
        }
        
        // Check if request body contains any data and reject it
        $requestContent = $request->getContent();
        
        // Only validate body content if it exists
        if ($requestContent !== null && $requestContent !== '') {
            // Parse JSON to check if it's empty or has data
            $jsonData = json_decode($requestContent, true);
            
            // If JSON is valid and not empty, reject it
            if ($jsonData !== null && !empty($jsonData)) {
                return response()->error('No body content allowed. Only authentication token is required.', 422);
            }
            
            // If JSON is invalid, reject it
            if ($jsonData === null && $requestContent !== '{}' && $requestContent !== 'null') {
                return response()->error('Invalid JSON format. Only authentication token is required.', 422);
            }
        }
        
        return $next($request);
    }
}

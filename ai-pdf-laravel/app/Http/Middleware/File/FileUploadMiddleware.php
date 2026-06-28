<?php

namespace App\Http\Middleware\File;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;


class FileUploadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Step 1: Log file upload attempt
        Log::info('File upload attempt', [
            'user_id' => $request->input('user_id'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        // Step 2: Check user permissions (additional authorization)
        $userId = $request->input('user_id');
        if (!$this->hasUploadPermission($userId)) {
            Log::warning('File upload denied - insufficient permissions', [
                'user_id' => $userId,
                'ip_address' => $request->ip()
            ]);
            return response()->error('Insufficient permissions to upload files', 403);
        }

        // Step 3: Additional security checks
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Check for malicious file content
            if ($this->isSuspiciousFile($file)) {
                Log::alert('Suspicious file upload blocked', [
                    'user_id' => $userId,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'ip_address' => $request->ip()
                ]);
                return response()->error('File appears to be suspicious and cannot be uploaded', 400);
            }
        }

        // Step 4: Continue to Request validation and controller
        $response = $next($request);

        // Step 5: Log successful upload (after controller execution)
        if ($response->getStatusCode() === 201) {
            Log::info('File upload successful', [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
        }

        return $response;
    }

    /**
     * Check if user has upload permission
     */
    private function hasUploadPermission(?string $userId): bool
    {
        // Add your permission logic here
        // For now, allow all authenticated users
        return !empty($userId);
    }

    /**
     * Check for suspicious file content
     */
    private function isSuspiciousFile($file): bool
    {
        // Add security checks here
        // Check file headers, scan for malware signatures, etc.

        // Example: Check if file size is suspiciously small for claimed type
        if ($file->getMimeType() === 'application/pdf' && $file->getSize() < 100) {
            return true;
        }

        return false;
    }
}

<?php

namespace App\Http\Middleware\File;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\File;
use Symfony\Component\HttpFoundation\Response;


class FileDeleteMiddleware
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
        $userId = $request->input('user_id');
        $fileId = $request->input('file_id');

        // Step 1: Log deletion attempt
        Log::info('File deletion attempt', [
            'user_id' => $userId,
            'file_id' => $fileId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        // Step 2: Get file info for audit trail
        $file = File::where('_id', $fileId)->where('user_id', $userId)->first();
        if ($file) {
            // Step 3: Check if file has active services
            if ($this->hasActiveServices($file)) {
                Log::warning('File deletion blocked - has active services', [
                    'user_id' => $userId,
                    'file_id' => $fileId,
                    'file_name' => $file->file_name
                ]);
                return response()->error('Cannot delete file with active processing services', 409);
            }

            // Step 4: Create audit trail before deletion
            $this->createAuditTrail($file, $userId, $request->ip());
        }

        // Step 5: Continue to Request validation and controller
        $response = $next($request);

        // Step 6: Log successful deletion
        if ($response->getStatusCode() === 200) {
            Log::info('File deletion successful', [
                'user_id' => $userId,
                'file_id' => $fileId,
                'file_name' => $file ? $file->file_name : 'unknown',
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
        }

        return $response;
    }

    /**
     * Check if file has active services
     */
    private function hasActiveServices(File $file): bool
    {
        return $file->services()
                   ->whereIn('status', ['pending', 'processing'])
                   ->exists();
    }

    /**
     * Create audit trail for file deletion
     */
    private function createAuditTrail(File $file, string $userId, string $ipAddress): void
    {
        Log::channel('audit')->info('File deletion audit', [
            'action' => 'file_deletion',
            'user_id' => $userId,
            'file_id' => $file->_id,
            'file_name' => $file->file_name,
            'file_size' => $file->file_size,
            'file_type' => $file->mime_type,
            'ip_address' => $ipAddress,
            'timestamp' => now(),
            'file_path' => $file->file_path
        ]);
    }
}

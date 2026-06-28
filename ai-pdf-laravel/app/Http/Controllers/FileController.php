<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Http\Requests\File\UploadFileRequest;
use App\Http\Requests\File\DeleteFileRequest;
use App\Http\Resources\FileResource;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    /**
     * Upload a file (PDF or Image)
     *
     * POST /api/file/upload
     * All validation handled by UploadFileRequest
     */
    public function upload(UploadFileRequest $request): JsonResponse
    {
        $user = $request->input('user') ?? $request->user;
        $userId = $user->_id ?? $user->id;

        $file = File::uploadFile($request->file('file'), $userId);

        return response()->success(
            new FileResource($file),
            'File uploaded successfully',
            201
        );
    }

    /**
     * Delete a file
     *
     * DELETE /api/file/delete
     * All validation handled by DeleteFileRequest
     */
    public function delete(DeleteFileRequest $request): JsonResponse
    {
        $user = $request->input('user') ?? $request->user;
        $userId = $user->_id ?? $user->id;
        
        $file = File::where('_id', $request->input('file_id'))
                    ->where('user_id', $userId)
                    ->firstOrFail();

        $file->deleteFile();

        return response()->success(null, 'File deleted successfully');
    }
}

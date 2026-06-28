<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;

/**
 * File Model for MongoDB
 * Stores uploaded PDF and image files
 */
class File extends Model
{
    // Use MongoDB connection
    protected $connection = 'mongodb';
    
    // Collection name in MongoDB
    protected $collection = 'files';
    
    // Fields that can be mass assigned
    protected $fillable = [
        'user_id',           // ID of user who uploaded file
        'file_name',         // Original filename (e.g., "document.pdf")
        'file_path',         // Path where file is stored (legacy or path reference)
        'gridfs_id',         // MongoDB GridFS ID
        'file_size',         // Size of file in bytes
        'mime_type',         // File type (application/pdf, image/jpeg, etc.)
        'file_extension',    // File extension (.pdf, .jpg, .png)
        'upload_status',     // Status: uploaded, processing, completed, failed
        'created_at',        // When file was uploaded
        'updated_at'         // When file was last updated
    ];
    
    // Cast attributes to specific types
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Default values for new records
    protected $attributes = [
        'upload_status' => 'uploaded'
    ];
    
    /**
     * Check if file is a PDF
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
    
    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/jpg', 
            'image/png'
        ]);
    }
    
    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Get services associated with this file
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'file_id', '_id');
    }

    /**
     * Upload and store a file in GridFS
     */
    public static function uploadFile(UploadedFile $uploadedFile, string $userId): self
    {
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(8);
        $uniqueFileName = "{$cleanName}_{$timestamp}_{$randomString}.{$extension}";
        
        $stream = fopen($uploadedFile->getRealPath(), 'rb');
        $bucket = DB::connection('mongodb')->getDatabase()->selectGridFSBucket();
        $gridFsId = $bucket->uploadFromStream($uniqueFileName, $stream);
        fclose($stream);
        
        return self::create([
            'user_id' => $userId,
            'file_name' => $originalName,
            'file_path' => "gridfs://{$gridFsId}",
            'gridfs_id' => (string) $gridFsId,
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
            'file_extension' => $extension,
            'upload_status' => 'uploaded'
        ]);
    }

    /**
     * Delete a file and its GridFS record
     */
    public function deleteFile(): bool
    {
        if (!empty($this->gridfs_id)) {
            try {
                $bucket = DB::connection('mongodb')->getDatabase()->selectGridFSBucket();
                $bucket->delete(new ObjectId($this->gridfs_id));
            } catch (\Exception $e) {
                // Ignore if already deleted from GridFS
            }
        }
        
        return $this->delete();
    }
}
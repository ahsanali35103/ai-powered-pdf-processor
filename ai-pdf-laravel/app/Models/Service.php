<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

/**
 * Service Model for MongoDB
 * Stores processing requests (OCR, Summarization, Translation)
 */
class Service extends Model
{
    // Use MongoDB connection
    protected $connection = 'mongodb';
    
    // Collection name in MongoDB
    protected $collection = 'services';
    
    // Fields that can be mass assigned
    protected $fillable = [
        'user_id',           // ID of user who requested service
        'organization_id',   // Organization ID (for multi-tenant support)
        'file_id',           // ID of file to process
        'type',              // Service type: ocr, summarization, translation
        'target_language',   // Target language for translation (optional)
        'status',            // Status: pending, processing, completed, failed
        'output',            // Output/result after processing
        'started_at',        // When processing started
        'completed_at',      // When processing completed
        'created_at',        // When service was requested
        'updated_at'         // When service was last updated
    ];
    
    // Cast attributes to specific types
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Default values for new records
    protected $attributes = [
        'status' => 'pending'
    ];
    
    // Valid service types
    const VALID_TYPES = ['ocr', 'summarization', 'translation'];
    
    // Valid statuses
    const VALID_STATUSES = ['pending', 'processing', 'completed', 'failed'];
    
    /**
     * Check if service type is valid
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, self::VALID_TYPES);
    }
    
    /**
     * Check if status is valid
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::VALID_STATUSES);
    }
    
    /**
     * Check if service is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    
    /**
     * Check if service is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    /**
     * Check if service is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
    
    /**
     * Check if service failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
    
    /**
     * Check if service requires target language
     */
    public function requiresTargetLanguage(): bool
    {
        return $this->type === 'translation';
    }
    
    /**
     * Get the file associated with this service
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', '_id');
    }
    
    /**
     * Scope to get services by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope to get services by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope to get pending services
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Create a new processing service request
     */
    public static function createService(array $data): self
    {
        $serviceData = [
            'user_id' => $data['user_id'],
            'organization_id' => $data['organization_id'] ?? null,
            'file_id' => $data['file_id'],
            'type' => $data['type'],
            'status' => 'pending'
        ];
        
        if ($data['type'] === 'translation' && !empty($data['target_language'])) {
            $serviceData['target_language'] = $data['target_language'];
        }
        
        return self::create($serviceData);
    }
    
    /**
     * Find service by ID for user
     */
    public static function findServiceById(string $serviceId, string $userId): ?self
    {
        return self::where('_id', $serviceId)
            ->where('user_id', $userId)
            ->with('file')
            ->first();
    }
    
    /**
     * Get all services for user
     */
    public static function getAllServices(string $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->with('file')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
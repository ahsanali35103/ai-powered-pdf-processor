<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use MongoDB\Laravel\Eloquent\Model;

class Organization extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'created_by',
    ];

    protected $hidden = [
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the users that belong to the organization
     */
    public function users()
    {
        return $this->hasMany(User::class, 'organization_id', 'id');
    }

    /**
     * Get the user who created the organization
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Create a new organization
     */
    public static function createOrganization(array $data, string $createdBy)
    {
        return self::create([
            'name' => $data['name'],
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Get the primary key type
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Get the value indicating whether the IDs are incrementing
     */
    public function getIncrementing()
    {
        return false;
    }
}

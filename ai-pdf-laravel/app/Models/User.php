<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'verification_code',
        'organization_id',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /**
     * Get the organization the user belongs to
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    /**
     * Get the session tokens for the user
     */
    public function sessionTokens()
    {
        return $this->hasMany(SessionToken::class);
    }

    /**
     * Get the forget tokens for the user
     */
    public function forgetTokens()
    {
        return $this->hasMany(SessionToken::class)->where('type', 'forgot_password_token');
    }

    /**
     * Add a new user with validation
     */
    public static function add($data)
    {
        // Handle if data is a request object with validated() method
        if (is_object($data) && method_exists($data, 'validated')) {
            $data = $data->validated();
        }

        // Normalize email to lowercase
        $data['email'] = strtolower(trim($data['email']));

        // Check if user exists
        $existingUser = self::where('email', $data['email'])->first();
        if ($existingUser) {
            abort(response()->json([
                'success' => false,
                'message' => 'User already exists with this email.',
            ], 409));
        }

        return self::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => $data['password'],
            'organization_id'   => $data['organization_id'] ?? null,
            'is_active'         => false,
            'verification_code' => '019daff012097167b604f574f3dbe63f',
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
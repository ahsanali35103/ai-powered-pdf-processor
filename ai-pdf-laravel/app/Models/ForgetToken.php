<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ForgetToken extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'token',
        'type',
        'user_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Find a valid token by type
     */
    public static function findValidToken(string $token, string $type): ?self
    {
        return self::where('token', $token)
            ->where('type', $type)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Generate a new token for user
     */
    public static function generate(string $type, User $user): string
    {
        $token = (string) random_int(100000, 999999);
        
        self::create([
            'token' => $token,
            'type' => $type,
            'user_id' => $user->id,
            'expires_at' => now()->addSeconds(360), // Password reset token expires in 2 hours
        ]);

        return $token;
    }
}

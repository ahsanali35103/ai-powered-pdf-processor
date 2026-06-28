<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SessionToken extends Model
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
    public static function findValidToken(?string $token, string $type): ?self
    {
        if (!$token) {
            return null;
        }

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
        // 6-digit OTP for verification and password reset, 60-char token for session access
        if ($type === 'signup_verification_token' || $type === 'forgot_password_token') {
            $token = (string) random_int(100000, 999999);
            $expiresAt = now()->addMinutes(10); // OTPs expire in 10 minutes
        } else {
            $token = str()->random(60);
            $expiresAt = now()->addDays(30); // Access tokens expire in 30 days
        }
        
        self::create([
            'token'      => $token,
            'type'       => $type,
            'user_id'    => $user->id,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }
}

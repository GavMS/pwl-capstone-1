<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * OtpVerification Model
 *
 * Stores one-time passwords for email verification during registration.
 * OTP codes expire after 10 minutes and can only be used once.
 *
 * Used by: Auth\RegisteredUserController (store, verifyOtp, resendOtp).
 */
class OtpVerification extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used'    => 'boolean',
    ];

    /**
     * Check if this OTP is still valid (not expired and not used).
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}

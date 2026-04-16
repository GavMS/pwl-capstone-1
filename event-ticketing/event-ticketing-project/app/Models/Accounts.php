<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Accounts Model (User)
 *
 * The authentication model for all users. Supports three roles: admin, organizer, user.
 * Extends Authenticatable for Laravel's built-in auth system.
 *
 * Used by: Auth controllers, RoleMiddleware, EventController, UserController, and more.
 */
class Accounts extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory, Notifiable;

    protected static function newFactory()
    {
        return \Database\Factories\AccountFactory::new();
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Role Helpers ────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    // ─── Relationships ───────────────────────────────────

    /** Events managed by this account (organizer role) */
    public function events()
    {
        return $this->hasMany(Events::class, 'organizer_id', 'id');
    }

    // ─── Notifications ───────────────────────────────────

    /** Use custom reset password email template */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }
}

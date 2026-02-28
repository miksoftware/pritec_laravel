<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'status',
        'is_admin',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    /**
     * Boot: prevent admin user from being deleted
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if ($user->is_admin) {
                throw new \Exception('El usuario administrador no puede ser eliminado.');
            }
        });
    }

    /**
     * Check if user can be deleted
     */
    public function isDeletable(): bool
    {
        return !$this->is_admin;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Login logs relationship
     */
    public function loginLogs()
    {
        return $this->hasMany(UserLoginLog::class);
    }

    /**
     * Record a login event
     */
    public function recordLogin(): void
    {
        $this->update(['last_login' => now()]);

        $this->loginLogs()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

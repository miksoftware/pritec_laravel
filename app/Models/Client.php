<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'identification',
        'phone',
        'address',
        'email',
        'status',
    ];

    /**
     * Get full name accessor
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope: exclude soft-deleted
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('status', '!=', 'deleted');
    }

    /**
     * Scope: only active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get statistics
     */
    public static function getStatistics(): array
    {
        $total = static::notDeleted()->count();
        $active = static::where('status', 'active')->count();
        $inactive = static::where('status', 'inactive')->count();
        $today = static::notDeleted()->whereDate('created_at', today())->count();
        $thisWeek = static::notDeleted()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonth = static::notDeleted()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        return compact('total', 'active', 'inactive', 'today', 'thisWeek', 'thisMonth');
    }
}

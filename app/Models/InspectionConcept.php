<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionConcept extends Model
{
    protected $fillable = ['name', 'category', 'display_order', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where(function ($q) use ($category) {
            $q->where('category', $category)->orWhere('category', 'all');
        });
    }

    /**
     * Get concepts for a given section, ordered properly
     */
    public static function getForSection(string $section): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->forCategory($section)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedRecord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'model_type', 
        'model_id', 
        'is_featured', 
        'featured_until',
        'featured_start_date', // Added for test compatibility
        'featured_end_date',   // Added for test compatibility
        'owner_id',
        'owner_type',
        'user_id',
        'stripe_id',
        'start_time',
        'end_time',
        'meta',
        'is_active',
        'settings',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_featured', true);
    }

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean', 
            'featured_until' => 'datetime', 
            'created_at' => 'datetime', 
            'updated_at' => 'datetime',
            'is_active' => 'boolean',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'meta' => 'array',
            'settings' => 'array'
        ];
    }
}

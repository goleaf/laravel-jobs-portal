<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueryOptimization
{
    /**
     * Scope for active records
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for recent records
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }
    
    /**
     * Scope for published records
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
    
    /**
     * Scope for valid jobs (not expired)
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('expires_on', '>', now());
    }
    
    /**
     * Scope for featured records
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope for searching by name/title
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('title', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }
    
    /**
     * Scope for filtering by location
     */
    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where(function ($q) use ($location) {
            $q->where('city', 'LIKE', "%{$location}%")
              ->orWhere('state', 'LIKE', "%{$location}%")
              ->orWhere('country', 'LIKE', "%{$location}%");
        });
    }
    
    /**
     * Scope for salary range filtering
     */
    public function scopeSalaryRange(Builder $query, ?int $minSalary = null, ?int $maxSalary = null): Builder
    {
        if ($minSalary) {
            $query->where('salary_from', '>=', $minSalary);
        }
        
        if ($maxSalary) {
            $query->where('salary_to', '<=', $maxSalary);
        }
        
        return $query;
    }
}
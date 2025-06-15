<?php

namespace App\Traits;

use JustBetter\UniqueValues\Support\UniqueValue;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Trait for generating unique values in job portal models
 */
trait HasUniqueValues
{
    /**
     * Generate unique reference for the model
     */
    public function generateUniqueReference(string $prefix = null): string
    {
        $modelName = class_basename(static::class);
        $prefix = $prefix ?? strtoupper(substr($modelName, 0, 3));
        $scope = strtolower($modelName) . '-references';
        
        return UniqueValue::make()
            ->scope($scope)
            ->attempts(10)
            ->generator(function (int $attempt) use ($prefix): string {
                $timestamp = Carbon::now()->format('ymd');
                $counter = str_pad((string) (1000 + $attempt), 4, '0', STR_PAD_LEFT);
                return "{$prefix}-{$timestamp}-{$counter}";
            })
            ->generate();
    }

    /**
     * Generate unique slug for the model
     */
    public function generateUniqueSlug(string $title, string $field = 'slug'): string
    {
        $modelName = class_basename(static::class);
        $scope = strtolower($modelName) . '-slugs';
        $baseSlug = Str::slug($title);
        
        return UniqueValue::make()
            ->scope($scope)
            ->attempts(20)
            ->generator(function (int $attempt) use ($baseSlug): string {
                return $attempt === 0 ? $baseSlug : "{$baseSlug}-{$attempt}";
            })
            ->generate();
    }

    /**
     * Generate unique code for the model
     */
    public function generateUniqueCode(string $prefix = null, int $length = 8): string
    {
        $modelName = class_basename(static::class);
        $prefix = $prefix ?? strtoupper(substr($modelName, 0, 3));
        $scope = strtolower($modelName) . '-codes';
        
        return UniqueValue::make()
            ->scope($scope)
            ->attempts(15)
            ->generator(function (int $attempt) use ($prefix, $length): string {
                $random = strtoupper(Str::random($length));
                $suffix = $attempt > 0 ? "-{$attempt}" : '';
                return "{$prefix}-{$random}{$suffix}";
            })
            ->generate();
    }
} 
<?php

namespace App\Views;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Enhanced Base Template Model for Laravel Job Portal
 *
 * Based on Habr article patterns for model-oriented templating
 * Since we're using a simplified approach without full Prosopo Views integration,
 * we'll create our own base class with similar functionality
 */
abstract class BaseTemplateModel
{
    /**
     * Get formatted date for templates
     */
    public function formatDate($date, string $format = 'Y-m-d H:i:s'): string
    {
        if ($date instanceof Carbon) {
            return $date->format($format);
        }

        if (is_string($date)) {
            return Carbon::parse($date)->format($format);
        }

        return '';
    }

    /**
     * Get human readable date
     */
    public function humanDate($date): string
    {
        if ($date instanceof Carbon) {
            return $date->diffForHumans();
        }

        if (is_string($date)) {
            return Carbon::parse($date)->diffForHumans();
        }

        return '';
    }

    /**
     * Format currency
     */
    public function formatCurrency($amount, string $currency = 'USD'): string
    {
        return number_format((float) $amount, 2).' '.$currency;
    }

    /**
     * Truncate text with ellipsis
     */
    public function truncate(string $text, int $length = 100): string
    {
        return strlen($text) > $length
            ? substr($text, 0, $length).'...'
            : $text;
    }

    /**
     * Convert collection to array for template usage
     */
    public function collectionToArray($collection): array
    {
        if ($collection instanceof Collection) {
            return $collection->toArray();
        }

        return is_array($collection) ? $collection : [];
    }

    /**
     * Get asset URL
     */
    public function asset(string $path): string
    {
        return asset($path);
    }

    /**
     * Get route URL
     */
    public function route(string $name, array $parameters = []): string
    {
        return route($name, $parameters);
    }

    /**
     * Get translation
     */
    public function trans(string $key, array $replace = []): string
    {
        return __($key, $replace);
    }

    /**
     * Check if user has permission
     */
    public function can(string $permission): bool
    {
        return auth()->check() && auth()->user()->can($permission);
    }

    /**
     * Get current user
     */
    public function currentUser()
    {
        return auth()->user();
    }

    /**
     * Get setting value
     */
    public function setting(string $key, $default = null)
    {
        return app('settings')->get($key, $default);
    }

    /**
     * Generate unique value
     */
    public function generateUnique(string $type, array $params = []): string
    {
        return app(\App\Services\Universal\UniversalUniqueValueService::class)
            ->generate($type, $params);
    }

    /**
     * Get SEO meta tags
     */
    public function seoMeta(string $title = '', string $description = '', array $keywords = []): array
    {
        return [
            'title' => $title ?: $this->setting('site_title', 'Laravel Job Portal'),
            'description' => $description ?: $this->setting('site_description', 'Professional job portal'),
            'keywords' => ! empty($keywords) ? implode(', ', $keywords) : $this->setting('meta_keywords', ''),
        ];
    }

    /**
     * Get breadcrumb data
     */
    public function breadcrumb(array $items = []): array
    {
        $breadcrumb = [
            ['title' => 'Home', 'url' => route('home')],
        ];

        return array_merge($breadcrumb, $items);
    }

    /**
     * Format number with proper locale
     */
    public function formatNumber($number, int $decimals = 0): string
    {
        return number_format((float) $number, $decimals);
    }

    /**
     * Get status badge class
     */
    public function statusBadge(string $status): string
    {
        $badges = [
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'draft' => 'bg-purple-100 text-purple-800',
        ];

        return $badges[strtolower($status)] ?? $badges['inactive'];
    }

    /**
     * Check if model has property
     */
    public function hasProperty(string $property): bool
    {
        return property_exists($this, $property);
    }

    /**
     * Get model data as array for debugging
     */
    public function toDebugArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        $data = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $data[$name] = $this->$name ?? null;
        }

        return $data;
    }
}

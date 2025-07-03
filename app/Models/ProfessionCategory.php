<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ProfessionCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'parent_id',
        'level',
        'sort_order',
        'is_active',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get translations for this category
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProfessionCategoryTranslation::class);
    }

    /**
     * Get translation for specific locale
     */
    public function translation(string $locale = null): ?ProfessionCategoryTranslation
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    /**
     * Get name in current or specified locale
     */
    public function getName(string $locale = null): string
    {
        $translation = $this->translation($locale);
        
        if ($translation) {
            return $translation->name;
        }

        // Fallback to default language
        $fallback = $this->translation(config('app.locale', 'en'));
        return $fallback?->name ?? $this->code;
    }

    /**
     * Get description in current or specified locale
     */
    public function getDescription(string $locale = null): ?string
    {
        $translation = $this->translation($locale);
        
        if ($translation) {
            return $translation->description;
        }

        // Fallback to default language
        $fallback = $this->translation(config('app.locale', 'en'));
        return $fallback?->description;
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProfessionCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProfessionCategory::class, 'parent_id');
    }

    /**
     * Get all active child categories.
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Get all professions in this category.
     */
    public function professions(): HasMany
    {
        return $this->hasMany(Profession::class, 'category_id');
    }

    /**
     * Get all active professions in this category.
     */
    public function activeProfessions(): HasMany
    {
        return $this->professions()->where('is_active', true);
    }

    /**
     * Scope to get only root categories (level 1).
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get categories by level.
     */
    public function scopeLevel(Builder $query, int $level): Builder
    {
        return $query->where('level', $level);
    }

    /**
     * Scope ordered by sort order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the full category path (breadcrumb).
     */
    public function getPath(): array
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, $current);
            $current = $current->parent;
        }

        return $path;
    }

    /**
     * Check if this category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Check if this category has active children.
     */
    public function hasActiveChildren(): bool
    {
        return $this->activeChildren()->exists();
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Check if this category is ancestor of another category
     */
    public function isAncestorOf(ProfessionCategory $category): bool
    {
        return in_array($this->id, $category->getPath());
    }

    /**
     * Check if this category is descendant of another category
     */
    public function isDescendantOf(ProfessionCategory $category): bool
    {
        return $category->isAncestorOf($this);
    }

    /**
     * Get count of professions in this category and all subcategories.
     */
    public function getTotalProfessionsCountAttribute(): int
    {
        $count = $this->activeProfessions()->count();
        
        foreach ($this->activeChildren as $child) {
            $count += $child->total_professions_count;
        }
        
        return $count;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set level based on parent
        static::creating(function ($category) {
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                $category->level = $parent ? $parent->level + 1 : 1;
            } else {
                $category->level = 1;
            }
        });
    }
} 
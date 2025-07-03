<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Profession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'category_id',
        'isco_code',
        'skill_level',
        'is_active',
        'is_featured',
        'sort_order',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get translations for this profession
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProfessionTranslation::class);
    }

    /**
     * Get translation for specific locale
     */
    public function translation(string $locale = null): ?ProfessionTranslation
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
     * Get skills required in current or specified locale
     */
    public function getSkillsRequired(string $locale = null): ?array
    {
        $translation = $this->translation($locale);
        
        if ($translation && $translation->skills_required) {
            return $translation->skills_required;
        }

        // Fallback to default language
        $fallback = $this->translation(config('app.locale', 'en'));
        return $fallback?->skills_required;
    }

    /**
     * Get education requirements in current or specified locale
     */
    public function getEducationRequirements(string $locale = null): ?array
    {
        $translation = $this->translation($locale);
        
        if ($translation && $translation->education_requirements) {
            return $translation->education_requirements;
        }

        // Fallback to default language
        $fallback = $this->translation(config('app.locale', 'en'));
        return $fallback?->education_requirements;
    }

    /**
     * Get the category that owns this profession
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProfessionCategory::class);
    }

    /**
     * Get jobs that use this profession
     */
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class);
    }

    /**
     * Scope for active professions
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured professions
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for specific skill level
     */
    public function scopeSkillLevel(Builder $query, string $skillLevel): Builder
    {
        return $query->where('skill_level', $skillLevel);
    }

    /**
     * Scope ordered by sort order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Search professions by name or description in current locale
     */
    public function scopeSearch(Builder $query, string $term, string $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();
        
        return $query->whereHas('translations', function (Builder $translationQuery) use ($term, $locale) {
            $translationQuery->where('locale', $locale)
                ->where(function (Builder $textQuery) use ($term) {
                    $textQuery->where('name', 'LIKE', "%{$term}%")
                        ->orWhere('description', 'LIKE', "%{$term}%");
                });
        });
    }

    /**
     * Get full path including category hierarchy
     */
    public function getFullPath(string $locale = null): array
    {
        $path = $this->category->getPath();
        $path[] = $this;
        return $path;
    }

    /**
     * Check if profession has jobs
     */
    public function hasJobs(): bool
    {
        return $this->jobs()->exists();
    }

    /**
     * Get job count
     */
    public function getJobCount(): int
    {
        return $this->jobs()->count();
    }

    /**
     * Get active job count
     */
    public function getActiveJobCount(): int
    {
        return $this->jobs()->where('is_active', true)->count();
    }

    /**
     * Check if translation exists for locale
     */
    public function hasTranslation(string $locale): bool
    {
        return $this->translations()->where('locale', $locale)->exists();
    }

    /**
     * Create or update translation for specific locale
     */
    public function setTranslation(string $locale, array $data): ProfessionTranslation
    {
        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            $data
        );
    }

    /**
     * Get all available locales for this profession
     */
    public function getAvailableLocales(): array
    {
        return $this->translations()->pluck('locale')->toArray();
    }
} 
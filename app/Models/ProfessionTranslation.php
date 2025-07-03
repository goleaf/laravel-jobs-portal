<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ProfessionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profession_id',
        'locale',
        'name',
        'description',
        'skills_required',
        'education_requirements',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'skills_required' => 'array',
        'education_requirements' => 'array',
        'profession_id' => 'integer',
    ];

    /**
     * Get the profession that owns the translation.
     */
    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Scope to filter by locale.
     */
    public function scopeByLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope to filter by profession.
     */
    public function scopeByProfession(Builder $query, int $professionId): Builder
    {
        return $query->where('profession_id', $professionId);
    }

    /**
     * Get the language name for the locale.
     */
    public function getLanguageNameAttribute(): string
    {
        return match ($this->locale) {
            'en' => 'English',
            'lt' => 'Lietuvių',
            'ru' => 'Русский',
            'pl' => 'Polski',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'zh' => '中文',
            'ar' => 'العربية',
            'pt' => 'Português',
            'tr' => 'Türkçe',
            'it' => 'Italiano',
            'ja' => '日本語',
            'hi' => 'हिन्दी',
            default => $this->locale,
        };
    }

    /**
     * Check if translation is complete.
     */
    public function isComplete(): bool
    {
        return !empty($this->name) && !empty($this->description);
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentage(): int
    {
        $fields = ['name', 'description', 'skills_required', 'education_requirements'];
        $completed = 0;

        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                $completed++;
            }
        }

        return (int) (($completed / count($fields)) * 100);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure locale is lowercase
        static::creating(function ($translation) {
            $translation->locale = strtolower($translation->locale);
        });

        static::updating(function ($translation) {
            $translation->locale = strtolower($translation->locale);
        });
    }
} 
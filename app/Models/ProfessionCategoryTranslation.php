<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionCategoryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'profession_category_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * Get the profession category that owns this translation
     */
    public function professionCategory(): BelongsTo
    {
        return $this->belongsTo(ProfessionCategory::class);
    }

    /**
     * Get the language name for this translation
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
     * Check if translation is complete
     */
    public function isComplete(): bool
    {
        return !empty($this->name) && !empty($this->description);
    }
} 
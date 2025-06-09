<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FAQ
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FAQ whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|max:150',
        'description' => 'required',
    ];

    public $table = 'faqs';

    /**
     * @var string[]
     */
    public $fillable = [
        'title',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
        protected function casts(): array
    {
        return [

            'id' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        
        ];
    }


    /**
     * Scope for active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured FAQs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for searching FAQs by title or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recently created FAQs.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for popular FAQs (by views if column exists).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->limit($limit);
    }

    /**
     * Scope for alphabetically ordered FAQs.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope for by category if exists.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for ordering by creation date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest FAQs.
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }
}

    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Taggable Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property int $tag_id
 * @property string $taggable_type
 * @property int $taggable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * Enhanced Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder forTag(int $tagId)
 * @method static \Illuminate\Database\Eloquent\Builder forModel(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder forEntity(string $type, int $id)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder forJobs()
 * @method static \Illuminate\Database\Eloquent\Builder forCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder withTags()
 * @method static \Illuminate\Database\Eloquent\Builder withTaggable()
 * @method static \Illuminate\Database\Eloquent\Builder trending(int $days = 30)
 *
 * @mixin \Eloquent
 */
class Taggable extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'taggables';

    protected $fillable = [
        'tag_id',
        'taggable_type',
        'taggable_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'tag_id' => 'integer',
            'taggable_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public static $rules = [
        'tag_id' => 'required|integer|exists:tags,id',
        'taggable_type' => 'required|string|max:255',
        'taggable_id' => 'required|integer|min:1',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tag_id', 'taggable_type', 'taggable_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForTag($query, int $tagId)
    {
        return $query->where('tag_id', $tagId);
    }

    public function scopeForModel($query, string $type)
    {
        return $query->where('taggable_type', $type);
    }

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('taggable_type', $type)
                    ->where('taggable_id', $id);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopePopular($query, int $limit = 10)
    {
        return $query->selectRaw('tag_id, taggable_type, COUNT(*) as usage_count')
                    ->groupBy('tag_id', 'taggable_type')
                    ->orderBy('usage_count', 'desc')
                    ->limit($limit);
    }

    public function scopeForJobs($query)
    {
        return $query->where('taggable_type', 'App\\Models\\Job');
    }

    public function scopeForCandidates($query)
    {
        return $query->where('taggable_type', 'App\\Models\\Candidate');
    }

    public function scopeWithTags($query)
    {
        return $query->with('tag');
    }

    public function scopeWithTaggable($query)
    {
        return $query->with('taggable');
    }

    public function scopeTrending($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->selectRaw('tag_id, COUNT(*) as trend_count')
                    ->groupBy('tag_id')
                    ->having('trend_count', '>=', 3)
                    ->orderBy('trend_count', 'desc');
    }

    public function getTaggableTypeDisplayAttribute(): string
    {
        $typeMap = [
            'App\\Models\\Job' => 'Job',
            'App\\Models\\Candidate' => 'Candidate',
            'App\\Models\\Company' => 'Company',
            'App\\Models\\Post' => 'Post',
        ];

        return $typeMap[$this->taggable_type] ?? class_basename($this->taggable_type);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->tag?->name . ' → ' . $this->taggable_type_display;
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    public function clearCaches(): void
    {
        Cache::forget('taggables.popular');
        Cache::forget('taggables.trending');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });
    }
}

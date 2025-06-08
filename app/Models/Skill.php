<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Skill
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $is_active
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill default()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill custom()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill usedInJobs()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill usedByCandidates()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Skill trending(int $days = 7)
 *
 * @mixin \Eloquent
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $candidate
 * @property-read int|null $candidate_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 */
class Skill extends Model
{
    use HasFactory;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:skills,name|max:150',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public $table = 'skills';

    public $fillable = [
        'name',
        'description',
        'is_active',
        'is_default',
    ];

    /**
     * Default eager loading for performance
     */
    protected $with = [];

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
            'is_default' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear cache when skill is updated
        static::updated(function ($skill) {
            cache()->forget("skill.{$skill->id}");
            cache()->forget("skills.popular");
            cache()->forget("skills.active");
            cache()->tags(['skills', 'skill-' . $skill->id])->flush();
        });

        // Clear cache when skill is deleted
        static::deleted(function ($skill) {
            cache()->forget("skill.{$skill->id}");
            cache()->forget("skills.popular");
            cache()->forget("skills.active");
            cache()->tags(['skills', 'skill-' . $skill->id])->flush();
        });
    }

    public function candidate(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'candidate_skills');
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'jobs_skill');
    }

    public function jobsSkill(): HasMany
    {
        return $this->hasMany(Job::class, 'jobs_skill', 'job_id', 'skill_id');
    }

    /**
     * Scope for active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive skills.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default skills.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom skills.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for popular skills (skills with most candidates/jobs).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['candidate', 'jobs'])
                    ->orderByDesc('candidate_count')
                    ->orderByDesc('jobs_count')
                    ->limit($limit);
    }

    /**
     * Scope for skills by name search.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for skills used in jobs.
     */
    public function scopeUsedInJobs($query)
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for skills used by candidates.
     */
    public function scopeUsedByCandidates($query)
    {
        return $query->whereHas('candidate');
    }

    /**
     * Scope for alphabetically ordered skills.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for recently created skills.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                    ->orderByDesc('created_at');
    }

    /**
     * Scope for trending skills (recently used).
     */
    public function scopeTrending($query, int $days = 7)
    {
        return $query->whereHas('candidate', function ($q) use ($days) {
                     $q->where('created_at', '>=', now()->subDays($days));
                 })
                 ->orWhereHas('jobs', function ($q) use ($days) {
                     $q->where('created_at', '>=', now()->subDays($days));
                 })
                 ->withCount(['candidate', 'jobs'])
                 ->orderByDesc('candidate_count')
                 ->orderByDesc('jobs_count');
    }

    /**
     * Scope for skills with minimum usage threshold.
     */
    public function scopeMinUsage($query, int $minCount = 5)
    {
        return $query->withCount(['candidate', 'jobs'])
                    ->havingRaw('(candidate_count + jobs_count) >= ?', [$minCount]);
    }

    /**
     * Get skills usage statistics.
     */
    public function getUsageStatsAttribute(): array
    {
        return cache()->remember("skill.{$this->id}.usage_stats", 3600, function () {
            return [
                'candidates_count' => $this->candidate()->count(),
                'jobs_count' => $this->jobs()->count(),
                'total_usage' => $this->candidate()->count() + $this->jobs()->count(),
                'last_used' => max(
                    $this->candidate()->latest()->first()?->created_at ?? '1970-01-01',
                    $this->jobs()->latest()->first()?->created_at ?? '1970-01-01'
                ),
            ];
        });
    }

    /**
     * Check if skill is popular (above average usage).
     */
    public function isPopular(): bool
    {
        $totalUsage = $this->candidate()->count() + $this->jobs()->count();
        $averageUsage = cache()->remember('skills.average_usage', 3600, function () {
            return self::withCount(['candidate', 'jobs'])->get()
                      ->avg(function ($skill) {
                          return $skill->candidate_count + $skill->jobs_count;
                      });
        });

        return $totalUsage > $averageUsage;
    }
}

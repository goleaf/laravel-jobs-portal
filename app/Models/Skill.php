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
            'name' => 'string',
            'description' => 'string',
            'category' => 'string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_technical' => 'boolean',
            'is_soft_skill' => 'boolean',
            'popularity_score' => 'integer',
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
     * Scope for featured skills.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured skills.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for technical skills.
     */
    public function scopeTechnical($query)
    {
        return $query->where('is_technical', true);
    }

    /**
     * Scope for non-technical skills.
     */
    public function scopeNonTechnical($query)
    {
        return $query->where('is_technical', false);
    }

    /**
     * Scope for soft skills.
     */
    public function scopeSoftSkills($query)
    {
        return $query->where('is_soft_skill', true);
    }

    /**
     * Scope for hard skills.
     */
    public function scopeHardSkills($query)
    {
        return $query->where('is_soft_skill', false);
    }

    /**
     * Scope for skills by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for searching skills.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%");
    }

    /**
     * Scope for recent skills.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old skills.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular skills.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderBy('popularity_score', 'desc')->limit($limit);
    }

    /**
     * Scope for trending skills.
     */
    public function scopeTrending($query, int $limit = 10)
    {
        return $query->withCount(['candidates' => function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        }])
        ->orderBy('candidates_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for skills with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->has('candidates');
    }

    /**
     * Scope for skills without candidates.
     */
    public function scopeWithoutCandidates($query)
    {
        return $query->doesntHave('candidates');
    }

    /**
     * Scope for skills with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope for skills without jobs.
     */
    public function scopeWithoutJobs($query)
    {
        return $query->doesntHave('jobs');
    }

    /**
     * Scope for programming skills.
     */
    public function scopeProgramming($query)
    {
        return $query->where('category', 'programming')
                    ->orWhere('name', 'like', '%programming%')
                    ->orWhere('name', 'like', '%coding%')
                    ->orWhere('name', 'like', '%development%');
    }

    /**
     * Scope for design skills.
     */
    public function scopeDesign($query)
    {
        return $query->where('category', 'design')
                    ->orWhere('name', 'like', '%design%')
                    ->orWhere('name', 'like', '%ui%')
                    ->orWhere('name', 'like', '%ux%');
    }

    /**
     * Scope for marketing skills.
     */
    public function scopeMarketing($query)
    {
        return $query->where('category', 'marketing')
                    ->orWhere('name', 'like', '%marketing%')
                    ->orWhere('name', 'like', '%seo%')
                    ->orWhere('name', 'like', '%social media%');
    }

    /**
     * Scope for management skills.
     */
    public function scopeManagement($query)
    {
        return $query->where('category', 'management')
                    ->orWhere('name', 'like', '%management%')
                    ->orWhere('name', 'like', '%leadership%')
                    ->orWhere('name', 'like', '%project%');
    }

    /**
     * Scope for language skills.
     */
    public function scopeLanguages($query)
    {
        return $query->where('category', 'language')
                    ->orWhere('name', 'like', '%language%')
                    ->orWhere('name', 'like', '%english%')
                    ->orWhere('name', 'like', '%spanish%')
                    ->orWhere('name', 'like', '%french%');
    }

    /**
     * Scope for communication skills.
     */
    public function scopeCommunication($query)
    {
        return $query->where('category', 'communication')
                    ->orWhere('name', 'like', '%communication%')
                    ->orWhere('name', 'like', '%presentation%')
                    ->orWhere('name', 'like', '%writing%');
    }

    /**
     * Scope for skills by popularity score range.
     */
    public function scopeByPopularityRange($query, int $min, int $max)
    {
        return $query->whereBetween('popularity_score', [$min, $max]);
    }

    /**
     * Scope for highly popular skills.
     */
    public function scopeHighlyPopular($query, int $threshold = 80)
    {
        return $query->where('popularity_score', '>=', $threshold);
    }

    /**
     * Scope for emerging skills.
     */
    public function scopeEmerging($query)
    {
        return $query->where('created_at', '>=', now()->subYear())
                    ->where('popularity_score', '>=', 50);
    }

    /**
     * Scope for in-demand skills.
     */
    public function scopeInDemand($query, int $limit = 20)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('created_at', '>=', now()->subDays(30))
              ->where('status', 1);
        }])
        ->orderBy('jobs_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope for skills required by specific job.
     */
    public function scopeRequiredByJob($query, int $jobId)
    {
        return $query->whereHas('jobs', function ($q) use ($jobId) {
            $q->where('job_id', $jobId);
        });
    }

    /**
     * Scope for skills possessed by candidate.
     */
    public function scopePossessedByCandidate($query, int $candidateId)
    {
        return $query->whereHas('candidates', function ($q) use ($candidateId) {
            $q->where('candidate_id', $candidateId);
        });
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

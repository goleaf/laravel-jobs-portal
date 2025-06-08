<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SalaryPeriod
 *
 * @version June 23, 2020, 5:43 am UTC
 *
 * @property string $period
 * @property string $description
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SalaryPeriod whereDescription($value)
 */
class SalaryPeriod extends Model
{
    use HasFactory;
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'period' => 'required|unique:salary_periods,period|max:150',
    ];

    public $table = 'salary_periods';

    public $fillable = [
        'period',
        'description',
        'is_default',
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
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'salary_period_id');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'salary_period_id');
    }

    /**
     * Scope for active salary periods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default salary periods.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom salary periods.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for periods with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for periods with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->whereHas('candidates');
    }

    /**
     * Scope for searching periods by name or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('period', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for popular periods (with most jobs).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount(['jobs', 'candidates'])
                    ->orderByDesc('jobs_count')
                    ->orderByDesc('candidates_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered periods.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('period', 'asc');
    }

    /**
     * Scope for hourly periods.
     */
    public function scopeHourly($query)
    {
        return $query->where('period', 'like', '%hour%')
                    ->orWhere('period', 'like', '%hr%');
    }

    /**
     * Scope for daily periods.
     */
    public function scopeDaily($query)
    {
        return $query->where('period', 'like', '%day%')
                    ->orWhere('period', 'like', '%daily%');
    }

    /**
     * Scope for monthly periods.
     */
    public function scopeMonthly($query)
    {
        return $query->where('period', 'like', '%month%')
                    ->orWhere('period', 'like', '%monthly%');
    }

    /**
     * Scope for yearly periods.
     */
    public function scopeYearly($query)
    {
        return $query->where('period', 'like', '%year%')
                    ->orWhere('period', 'like', '%annual%');
    }

    /**
     * Scope for recent periods (created in last 30 days).
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old periods (created more than specified days ago).
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for inactive periods.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured periods.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for periods used in active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 'open')->where('expire_date', '>', now());
        });
    }

    /**
     * Scope for periods with available candidates.
     */
    public function scopeWithAvailableCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_available', true);
        });
    }
}

<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CandidateEducation
 *
 * @property int $id
 * @property int $candidate_id
 * @property int $degree_level_id
 * @property string $country
 * @property string|null $state
 * @property string|null $city
 * @property string $institute
 * @property string $result
 * @property int $year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Candidate $candidate
 *
 * @method static Builder|CandidateEducation newModelQuery()
 * @method static Builder|CandidateEducation newQuery()
 * @method static Builder|CandidateEducation query()
 * @method static Builder|CandidateEducation whereCandidateId($value)
 * @method static Builder|CandidateEducation whereCity($value)
 * @method static Builder|CandidateEducation whereCountry($value)
 * @method static Builder|CandidateEducation whereCreatedAt($value)
 * @method static Builder|CandidateEducation whereDegreeLevelId($value)
 * @method static Builder|CandidateEducation whereId($value)
 * @method static Builder|CandidateEducation whereInstitute($value)
 * @method static Builder|CandidateEducation whereResult($value)
 * @method static Builder|CandidateEducation whereState($value)
 * @method static Builder|CandidateEducation whereUpdatedAt($value)
 * @method static Builder|CandidateEducation whereYear($value)
 *
 * @mixin Eloquent
 *
 * @property string $degree_title
 * @property-read \App\Models\RequiredDegreeLevel $degreeLevel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CandidateEducation whereDegreeTitle($value)
 *
 * @property int|null $country_id
 * @property int|null $state_id
 * @property int|null $city_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CandidateEducation whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CandidateEducation whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CandidateEducation whereStateId($value)
 */
class CandidateEducation extends Model
{
    use HasFactory;
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'degree_title' => 'required|max:150',
        'country_id' => 'required',
        'institute' => 'required|max:150',
        'result' => 'required|max:150',
        'year' => 'required',
    ];

    public $table = 'candidate_educations';

    public $fillable = [
        'candidate_id',
        'degree_level_id',
        'degree_title',
        'country_id',
        'state_id',
        'city_id',
        'institute',
        'result',
        'year',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'candidate_id' => 'integer',
        'degree_level_id' => 'integer',
        'degree_title' => 'string',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city_id' => 'integer',
        'institute' => 'string',
        'result' => 'string',
        'year' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function degreeLevel(): BelongsTo
    {
        return $this->belongsTo(RequiredDegreeLevel::class, 'degree_level_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class)->withDefault();
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    /**
     * Scope for education by candidate.
     */
    public function scopeByCandidate($query, int $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    /**
     * Scope for education by degree level.
     */
    public function scopeByDegreeLevel($query, int $degreeLevelId)
    {
        return $query->where('degree_level_id', $degreeLevelId);
    }

    /**
     * Scope for education by country.
     */
    public function scopeByCountry($query, int $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Scope for education by state.
     */
    public function scopeByState($query, int $stateId)
    {
        return $query->where('state_id', $stateId);
    }

    /**
     * Scope for education by city.
     */
    public function scopeByCity($query, int $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope for education by year range.
     */
    public function scopeByYearRange($query, int $startYear, int $endYear)
    {
        return $query->whereBetween('year', [$startYear, $endYear]);
    }

    /**
     * Scope for recent education.
     */
    public function scopeRecent($query, int $years = 5)
    {
        return $query->where('year', '>=', now()->year - $years);
    }

    /**
     * Scope for old education.
     */
    public function scopeOld($query, int $years = 10)
    {
        return $query->where('year', '<', now()->year - $years);
    }

    /**
     * Scope for education by institute.
     */
    public function scopeByInstitute($query, string $institute)
    {
        return $query->where('institute', 'like', "%{$institute}%");
    }

    /**
     * Scope for education with high results.
     */
    public function scopeHighAchievers($query)
    {
        return $query->whereIn('result', ['A+', 'A', 'First Class', 'Distinction', 'Honors']);
    }

    /**
     * Scope for bachelor's degree education.
     */
    public function scopeBachelors($query)
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'like', '%bachelor%');
        });
    }

    /**
     * Scope for master's degree education.
     */
    public function scopeMasters($query)
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'like', '%master%');
        });
    }

    /**
     * Scope for PhD education.
     */
    public function scopePhd($query)
    {
        return $query->whereHas('degreeLevel', function ($q) {
            $q->where('name', 'like', '%phd%')
              ->orWhere('name', 'like', '%doctorate%');
        });
    }

    /**
     * Scope for alphabetical ordering by institute.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('institute', 'asc');
    }

    /**
     * Scope for ordering by year (newest first).
     */
    public function scopeNewestFirst($query)
    {
        return $query->orderBy('year', 'desc');
    }

    /**
     * Scope for ordering by year (oldest first).
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('year', 'asc');
    }

    /**
     * Scope for verified education.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified education.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for current education.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope for completed education.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_current', false);
    }

    /**
     * Get formatted education period.
     */
    public function getEducationPeriodAttribute(): string
    {
        if ($this->is_current) {
            return $this->start_date ? $this->start_date->format('Y') . ' - Present' : $this->year . ' - Present';
        }
        
        return $this->start_date && $this->end_date 
            ? $this->start_date->format('Y') . ' - ' . $this->end_date->format('Y')
            : (string) $this->year;
    }

    /**
     * Get full location.
     */
    public function getFullLocationAttribute(): string
    {
        $location = [];
        
        if ($this->city) {
            $location[] = $this->city->name;
        }
        if ($this->state) {
            $location[] = $this->state->name;
        }
        if ($this->country) {
            $location[] = $this->country->name;
        }
        
        return implode(', ', $location);
    }

    /**
     * Check if education is recent.
     */
    public function isRecent(int $years = 5): bool
    {
        return $this->year >= (now()->year - $years);
    }

    /**
     * Check if education is high achievement.
     */
    public function isHighAchievement(): bool
    {
        return in_array($this->result, ['A+', 'A', 'First Class', 'Distinction', 'Honors']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MaritalStatus
 *
 * @version May 14, 2020, 5:43 am UTC
 *
 * @property string $marital_status
 * @property string $description
 * @property int $id
 * @property bool $is_default
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MaritalStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

    /**
     * Scope a query to only include old records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOld(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("created_at", "asc");
    }




    /**
     * Get candidates that belong to this marital status.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Scope for active marital statuses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive marital statuses.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for default marital statuses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom marital statuses.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for marital statuses with candidates.
     */
    public function scopeWithCandidates($query)
    {
        return $query->whereHas('candidates');
    }

    /**
     * Scope for marital statuses with active candidates.
     */
    public function scopeWithActiveCandidates($query)
    {
        return $query->whereHas('candidates', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching marital statuses by name or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('marital_status', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for popular marital statuses (with most candidates).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('candidates')
                    ->orderByDesc('candidates_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered marital statuses.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('marital_status', 'asc');
    }

    /**
     * Scope for recently created marital statuses.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for single marital statuses.
     */
    public function scopeSingle($query)
    {
        return $query->where('marital_status', 'like', '%single%')
                    ->orWhere('marital_status', 'like', '%unmarried%')
                    ->orWhere('marital_status', 'like', '%never%married%');
    }

    /**
     * Scope for married marital statuses.
     */
    public function scopeMarried($query)
    {
        return $query->where('marital_status', 'like', '%married%')
                    ->orWhere('marital_status', 'like', '%spouse%');
    }

    /**
     * Scope for divorced/separated marital statuses.
     */
    public function scopeDivorced($query)
    {
        return $query->where('marital_status', 'like', '%divorced%')
                    ->orWhere('marital_status', 'like', '%separated%');
    }

    /**
     * Scope for widowed marital statuses.
     */
    public function scopeWidowed($query)
    {
        return $query->where('marital_status', 'like', '%widowed%')
                    ->orWhere('marital_status', 'like', '%widow%');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReportedToCompany
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob query()
 *
 * @mixin \Eloquent
 *
 * @property int $id
 * @property int $user_id
 * @property int $company_id
 * @property string $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReportedJob whereUserId($value)
 *
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\User $user
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


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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

    /**
     * Scope a query to only include recent records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to search records by name or relevant fields.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

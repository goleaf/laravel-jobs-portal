<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReportedToCandidate.
 *
 * @property int $id
 * @property int $user_id
 * @property int $candidate_id
 * @property string $note
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property Candidate $candidate
 * @property User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate whereCandidateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportedToCandidate whereUserId($value)
 *
 * @mixin \Eloquent
 */
class ReportedToCandidate extends Model
{
    public $table = 'reported_to_candidates';

    public $fillable = [
        'user_id',
        'candidate_id',
        'note',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'candidate_id' => 'integer',
        'note' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
}

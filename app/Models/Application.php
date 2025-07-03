<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Application Model - Job Applications.
 *
 * @property int $id
 * @property int $job_id
 * @property int $candidate_id
 * @property int $resume_id
 * @property float $expected_salary
 * @property null|string $notes
 * @property int $status
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 */
class Application extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The table associated with the model.
     */
    protected $table = 'job_applications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_id',
        'candidate_id',
        'resume_id',
        'expected_salary',
        'status',
        'notes',
    ];

    /**
     * Activity log configuration for spatie/laravel-activitylog.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['job_id', 'candidate_id', 'status', 'expected_salary'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Application has been {$eventName}");
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the job that the application is for.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the candidate who made the application.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'job_id' => 'integer',
            'candidate_id' => 'integer',
            'resume_id' => 'integer',
            'expected_salary' => 'decimal:2',
            'status' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default status
        static::creating(function ($model) {
            if (is_null($model->status)) {
                $model->status = 0; // Default to pending status
            }
        });
    }
}

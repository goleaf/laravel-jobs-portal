<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplicationSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationScheduleFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'job_application_id',
        'stage_id',
        'time',
        'date',
        'notes',
        'status',
        'batch',
        'rejected_slot_notes',
        'employer_cancel_slot_notes',
    ];

    /**
     * Get the job application that owns the schedule.
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Get the job stage that owns the schedule.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(JobStage::class);
    }

    /**
     * Cast attributes to native types.
     */
    protected function casts(): array
    {
        return [
            'job_application_id' => 'integer',
            'stage_id' => 'integer',
            'status' => 'integer',
            'batch' => 'integer',
            'date' => 'date',
        ];
    }
}

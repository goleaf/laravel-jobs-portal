<?php

namespace App\Repositories;

use App\Models\Job;
use Illuminate\Container\Container as Application;

class JobRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'job_title',
        'job_description'
    ];

    public function model()
    {
        return Job::class;
    }

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
} 
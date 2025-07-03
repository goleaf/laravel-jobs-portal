<?php

namespace App\Repositories;

use App\Models\Application;
use App\Repositories\Contracts\ApplicationRepositoryInterface;

class ApplicationRepository extends BaseRepository implements ApplicationRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Application::class;

    /**
     * Get applications by user ID.
     *
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * Get applications by job ID.
     *
     * @return mixed
     */
    public function getByJobId(int $jobId)
    {
        return $this->model->where('job_id', $jobId)->get();
    }
}

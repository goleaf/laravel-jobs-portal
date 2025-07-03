<?php

namespace App\Repositories;

use App\Models\EmailJob;
use App\Repositories\Contracts\EmailJobRepositoryInterface;

class EmailJobRepository extends BaseRepository implements EmailJobRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = EmailJob::class;

    /**
     * Get email jobs by status.
     *
     * @return mixed
     */
    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }
}

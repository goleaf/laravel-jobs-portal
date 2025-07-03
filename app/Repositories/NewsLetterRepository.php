<?php

namespace App\Repositories;

use App\Models\NewsLetter;
use App\Repositories\Contracts\NewsLetterRepositoryInterface;

class NewsLetterRepository extends BaseRepository implements NewsLetterRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = NewsLetter::class;

    /**
     * Get newsletters by status.
     *
     * @return mixed
     */
    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }
}

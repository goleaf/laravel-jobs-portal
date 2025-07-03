<?php

namespace App\Repositories;

use App\Models\FavouriteJob;
use App\Repositories\Contracts\FavouriteJobRepositoryInterface;

class FavouriteJobRepository extends BaseRepository implements FavouriteJobRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = FavouriteJob::class;

    /**
     * Get favourite jobs by user ID.
     *
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}

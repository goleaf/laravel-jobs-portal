<?php

namespace App\Repositories;

use App\Models\CustomMedia;
use App\Repositories\Contracts\CustomMediaRepositoryInterface;

class CustomMediaRepository extends BaseRepository implements CustomMediaRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = CustomMedia::class;

    /**
     * Get media by type.
     *
     * @param string $type
     * @return mixed
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Get media by user ID.
     *
     * @param int $userId
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
} 
<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\Contracts\FileRepositoryInterface;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = File::class;

    /**
     * Get files by type.
     *
     * @param string $type
     * @return mixed
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Get files by user ID.
     *
     * @param int $userId
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
} 
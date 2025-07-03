<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Tag::class;

    /**
     * Get tags by type.
     *
     * @return mixed
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }
}

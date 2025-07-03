<?php

namespace App\Repositories;

use App\Models\Taggable;
use App\Repositories\Contracts\TaggableRepositoryInterface;

class TaggableRepository extends BaseRepository implements TaggableRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Taggable::class;

    /**
     * Get taggables by tag ID.
     *
     * @return mixed
     */
    public function getByTagId(int $tagId)
    {
        return $this->model->where('tag_id', $tagId)->get();
    }

    /**
     * Get taggables by taggable type and ID.
     *
     * @return mixed
     */
    public function getByTaggable(string $taggableType, int $taggableId)
    {
        return $this->model->where('taggable_type', $taggableType)
            ->where('taggable_id', $taggableId)
            ->get();
    }
}

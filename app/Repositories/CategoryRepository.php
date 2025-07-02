<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Category::class;

    /**
     * Get categories by type.
     *
     * @param string $type
     * @return mixed
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }
} 
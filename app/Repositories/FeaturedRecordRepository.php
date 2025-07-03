<?php

namespace App\Repositories;

use App\Models\FeaturedRecord;
use App\Repositories\Contracts\FeaturedRecordRepositoryInterface;

class FeaturedRecordRepository extends BaseRepository implements FeaturedRecordRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = FeaturedRecord::class;

    /**
     * Get featured records by type.
     *
     * @return mixed
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }
}

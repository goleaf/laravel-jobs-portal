<?php

namespace App\Repositories;

use App\Models\FavouriteCompany;
use App\Repositories\Contracts\FavouriteCompanyRepositoryInterface;

class FavouriteCompanyRepository extends BaseRepository implements FavouriteCompanyRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = FavouriteCompany::class;

    /**
     * Get favourite companies by user ID.
     *
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}

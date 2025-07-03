<?php

namespace App\Repositories;

use App\Models\CmsServices;
use App\Repositories\Contracts\CmsServicesRepositoryInterface;

class CmsServicesRepository extends BaseRepository implements CmsServicesRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = CmsServices::class;

    /**
     * Get services by status.
     *
     * @return mixed
     */
    public function getByStatus(string $status)
    {
        return $this->model->where('status', $status)->get();
    }
}

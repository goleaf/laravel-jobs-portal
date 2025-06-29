<?php

namespace App\Repositories;

use App\Models\FrontSetting;
use App\Repositories\Contracts\FrontSettingRepositoryInterface;

class FrontSettingRepository extends BaseRepository implements FrontSettingRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = FrontSetting::class;

    /**
     * Get setting by key.
     *
     * @param string $key
     * @return mixed
     */
    public function getByKey(string $key)
    {
        return $this->model->where('key', $key)->first();
    }
} 
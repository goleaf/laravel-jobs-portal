<?php

namespace App\Repositories;

use App\Models\EnvSetting;
use App\Repositories\Contracts\EnvSettingRepositoryInterface;

class EnvSettingRepository extends BaseRepository implements EnvSettingRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = EnvSetting::class;

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
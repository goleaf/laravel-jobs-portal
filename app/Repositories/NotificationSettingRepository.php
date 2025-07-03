<?php

namespace App\Repositories;

use App\Models\NotificationSetting;
use App\Repositories\Contracts\NotificationSettingRepositoryInterface;

class NotificationSettingRepository extends BaseRepository implements NotificationSettingRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = NotificationSetting::class;

    /**
     * Get settings by user ID.
     *
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}

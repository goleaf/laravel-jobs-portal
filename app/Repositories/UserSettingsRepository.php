<?php

namespace App\Repositories;

use App\Models\UserSettings;
use App\Repositories\Contracts\UserSettingsRepositoryInterface;

class UserSettingsRepository extends BaseRepository implements UserSettingsRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = UserSettings::class;

    /**
     * Get settings by user ID.
     *
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }
}

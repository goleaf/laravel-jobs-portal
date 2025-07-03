<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = Notification::class;

    /**
     * Get notifications by user ID.
     *
     * @return mixed
     */
    public function getByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * Get unread notifications by user ID.
     *
     * @return mixed
     */
    public function getUnreadByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->where('read', false)->get();
    }
}

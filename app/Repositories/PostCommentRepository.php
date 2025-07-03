<?php

namespace App\Repositories;

use App\Models\PostComment;
use App\Repositories\Contracts\PostCommentRepositoryInterface;

class PostCommentRepository extends BaseRepository implements PostCommentRepositoryInterface
{
    /**
     * @var string
     */
    protected $modelName = PostComment::class;

    /**
     * Get comments by post ID.
     *
     * @return mixed
     */
    public function getByPostId(int $postId)
    {
        return $this->model->where('post_id', $postId)->get();
    }
}

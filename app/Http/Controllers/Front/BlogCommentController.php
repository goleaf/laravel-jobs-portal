<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Front\StoreBlogCommentRequest;

class BlogCommentController extends Controller
{
    /**
     * Store a newly created blog comment.
     *
     * @param  mixed  $blog
     */
    public function store(StoreBlogCommentRequest $request, $blog): JsonResponse
    {
        // Store comment logic here

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully!',
        ]);
    }
}

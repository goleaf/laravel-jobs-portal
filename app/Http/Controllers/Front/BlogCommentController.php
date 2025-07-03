<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BlogCommentController extends Controller
{
    /**
     * Store a newly created blog comment.
     *
     * @param  mixed  $blog
     */
    public function store(StoreBlogCommentRequest $request, $blog): JsonResponse
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Store comment logic here

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully!',
        ]);
    }
}

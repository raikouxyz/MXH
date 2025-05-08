<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    /**
     * Toggle like status for a post.
     */
    public function toggle(Post $post): JsonResponse
    {
        $user = auth()->user();

        if ($post->isLikedBy($user)) {
            // Nếu đã like thì unlike
            $post->likes()->where('user_id', $user->id)->delete();
            $liked = false;
        } else {
            // Nếu chưa like thì like
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likes_count
        ]);
    }
}

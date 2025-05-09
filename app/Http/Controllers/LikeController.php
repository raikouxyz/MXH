<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
<<<<<<< Updated upstream
     * Toggle like for a post
=======
     * Toggle like status for a post
>>>>>>> Stashed changes
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request, Post $post)
    {
        $user = auth()->user();
        
<<<<<<< Updated upstream
        // Kiểm tra xem người dùng đã like bài viết chưa
        $existingLike = $post->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            // Nếu đã like, xóa like (unlike)
            $existingLike->delete();
            $reaction = null;
        } else {
            // Nếu chưa like, tạo like mới
            $post->likes()->create([
                'user_id' => $user->id,
                'type' => 'like'
            ]);
            $reaction = 'like';
        }

        return response()->json([
            'reaction' => $reaction,
            'likesCount' => $post->getLikesCount()
=======
        if ($post->isLikedBy($user->id)) {
            // Unlike
            $post->likes()->where('user_id', $user->id)->delete();
            $liked = false;
        } else {
            // Like
            $post->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        $likeCount = $post->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount
>>>>>>> Stashed changes
        ]);
    }
} 
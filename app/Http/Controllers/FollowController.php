<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    // Theo dõi
    public function follow($userId)
    {
        $userToFollow = User::findOrFail($userId);

        if ($userToFollow->id === Auth::id()) {
            return response()->json(['message' => 'Bạn không thể tự theo dõi chính mình'], 400);
        }

        $existingFollow = Follow::where('follower_id', Auth::id())
            ->where('following_id', $userToFollow->id)
            ->first();

        if ($existingFollow) {
            return response()->json(['message' => 'Bạn đã theo dõi người này rồi'], 400);
        }

        Follow::create([
            'follower_id' => Auth::id(),
            'following_id' => $userToFollow->id
        ]);

        return response()->json(['message' => 'Theo dõi thành công']);
    }

    // Bỏ theo dõi
    public function unfollow($userId)
    {
        $userToUnfollow = User::findOrFail($userId);

        $follow = Follow::where('follower_id', Auth::id())
            ->where('following_id', $userToUnfollow->id)
            ->first();

        if (!$follow) {
            return response()->json(['message' => 'Bạn chưa theo dõi người này'], 400);
        }

        $follow->delete();

        return response()->json(['message' => 'Bỏ theo dõi thành công']);
    }
}

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
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập!'], 401);
        }
        if ($userId == auth()->id()) {
            return response()->json(['message' => 'Không thể tự theo dõi chính mình!'], 400);
        }
        $userToFollow = User::findOrFail($userId);
        $exists = Follow::where('follower_id', auth()->id())
            ->where('following_id', $userToFollow->id)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Bạn đã theo dõi người này rồi!'], 400);
        }
        Follow::create([
            'follower_id' => auth()->id(),
            'following_id' => $userToFollow->id
        ]);
        return response()->json(['message' => 'Theo dõi thành công!']);
    }

    // Bỏ theo dõi
    public function unfollow($userId)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập!'], 401);
        }
        $userToUnfollow = User::findOrFail($userId);
        $follow = Follow::where('follower_id', auth()->id())
            ->where('following_id', $userToUnfollow->id)
            ->first();
        if ($follow) {
            $follow->delete();
        }
        return response()->json(['message' => 'Bỏ theo dõi thành công!']);
    }

    public function followingList(User $user)
    {
        $following = $user->following;
        $html = '';
        foreach ($following as $followingUser) {
            $html .= '<div class="col-md-4 mb-3 following-item" data-user-id="'.$followingUser->id.'">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="'.($followingUser->avatar ? asset('images/' . $followingUser->avatar) : asset('images/default-avatar.jpg')).'"
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 alt="'.$followingUser->name.'\'s avatar">
                            <div>
                                <h5 class="mb-0">'.$followingUser->name.'</h5>
                                <p class="text-muted mb-0">'.$followingUser->email.'</p>
                            </div>
                        </div>';
            if (auth()->id() !== $followingUser->id) {
                $isFollowing = auth()->user()->isFollowing($followingUser);
                $html .= '<div class="mt-3">
                    <button class="btn '.($isFollowing ? 'btn-primary' : 'btn-outline-primary').' btn-sm follow-button" data-user-id="'.$followingUser->id.'">
                        <i class="fas fa-user-plus"></i>
                        <span class="follow-text">'.($isFollowing ? 'Bỏ theo dõi' : 'Theo dõi').'</span>
                    </button>
                </div>';
            }
            $html .= '</div></div></div>';
        }
        if ($following->count() == 0) {
            $html = '<div class="col-12"><div class="alert alert-info">Chưa theo dõi ai.</div></div>';
        }
        return $html;
    }
}

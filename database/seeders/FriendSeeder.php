<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        // Tạo mối quan hệ bạn bè cho mỗi người dùng
        foreach ($users as $user) {
            // Mỗi người dùng sẽ có 3-7 người bạn
            $numberOfFriends = fake()->numberBetween(3, 7);
            
            // Lấy danh sách người dùng khác (trừ chính mình)
            $potentialFriends = $users->where('id', '!=', $user->id);
            
            // Chọn ngẫu nhiên số lượng bạn bè
            $friends = $potentialFriends->random($numberOfFriends);
            
            foreach ($friends as $friend) {
                // Kiểm tra xem mối quan hệ đã tồn tại chưa
                $exists = DB::table('friends')
                    ->where(function($query) use ($user, $friend) {
                        $query->where('user_id', $user->id)
                              ->where('friend_id', $friend->id);
                    })
                    ->orWhere(function($query) use ($user, $friend) {
                        $query->where('user_id', $friend->id)
                              ->where('friend_id', $user->id);
                    })
                    ->exists();
                
                if (!$exists) {
                    // Tạo mối quan hệ bạn bè hai chiều
                    DB::table('friends')->insert([
                        [
                            'user_id' => $user->id,
                            'friend_id' => $friend->id,
                            'status' => 'accepted',
                            'created_at' => now(),
                            'updated_at' => now()
                        ],
                        [
                            'user_id' => $friend->id,
                            'friend_id' => $user->id,
                            'status' => 'accepted',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);
                }
            }
        }
    }
} 
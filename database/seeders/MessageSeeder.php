<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $stickers = ['sticker1.png', 'sticker2.png', 'sticker3.png', 'sticker4.png'];
        $emojis = ['😊', '👍', '❤️', '🎉', ''];

        foreach ($users as $user) {
            // Lấy danh sách bạn bè của người dùng
            $friends = DB::table('friends')
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->pluck('friend_id')
                ->toArray();

            // Tạo tin nhắn cho mỗi người bạn
            foreach ($friends as $friendId) {
                // Tạo 5-15 tin nhắn cho mỗi cặp bạn bè
                $numberOfMessages = fake()->numberBetween(5, 15);
                
                for ($i = 0; $i < $numberOfMessages; $i++) {
                    // Luân phiên người gửi và người nhận
                    $senderId = $i % 2 === 0 ? $user->id : $friendId;
                    $receiverId = $i % 2 === 0 ? $friendId : $user->id;

                    Message::create([
                        'sender_id' => $senderId,
                        'receiver_id' => $receiverId,
                        'content' => fake()->optional(0.7)->sentence(), // 70% tin nhắn có nội dung
                        'image_path' => fake()->optional(0.2)->imageUrl(), // 20% tin nhắn có ảnh
                        'sticker' => fake()->optional(0.2)->randomElement($stickers), // 20% tin nhắn có sticker
                        'emoji' => fake()->optional(0.1)->randomElement($emojis), // 10% tin nhắn có emoji
                        'is_read' => fake()->boolean(),
                        'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

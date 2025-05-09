<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SocialSeeder extends Seeder
{
    public function run(): void
    {
        // Tạm tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate theo đúng thứ tự phụ thuộc
        DB::table('likes')->truncate();        // phụ thuộc post_id
        DB::table('posts')->truncate();
        DB::table('group_user')->truncate();
        DB::table('groups')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1'); // Bật lại

        // Tạo 5 user mẫu
        $users = User::factory()->count(5)->create()->each(function ($user, $i) {
            $user->name = "User " . ($i + 1);
            $user->email = "user{$i}@test.com";
            $user->avatar = "https://via.placeholder.com/80?text=U" . ($i + 1);
            $user->password = Hash::make('password');
            $user->save();
        });

        foreach ([0, 1] as $i) {
            $group = Group::create([
                'name' => 'Nhóm mẫu ' . ($i + 1),
                'description' => 'Đây là mô tả cho nhóm mẫu ' . ($i + 1),
                'privacy' => 'public',
                'user_id' => $users[$i]->id,
                'image' => 'https://via.placeholder.com/150?text=Group' . ($i + 1),
            ]);

            // Thêm người tạo là admin
            $group->members()->attach($users[$i]->id, ['role' => 'admin']);

            // Thêm 2 user khác làm member
            $group->members()->attach($users[$i + 2]->id, ['role' => 'member']);
            $group->members()->attach($users[$i + 3]->id, ['role' => 'member']);

            // Mỗi thành viên đăng 1 bài viết mẫu
            foreach ($group->members as $member) {
                Post::create([
                    'user_id' => $member->id,
                    'group_id' => $group->id,
                    'content' => fake()->sentence(10),
                ]);
            }
        }
    }
}

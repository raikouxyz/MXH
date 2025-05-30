<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class FriendAndLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users and posts
        $users = User::all();
        $posts = Post::all();
        
        // Create friend relationships
        foreach ($users as $user) {
            // Get random number of friends (between 1 and 5)
            $numberOfFriends = rand(1, 5);
            
            // Get random users that are not the current user
            $potentialFriends = $users->where('id', '!=', $user->id)->random($numberOfFriends);
            
            foreach ($potentialFriends as $friend) {
                // Check if friendship already exists
                $exists = DB::table('friends')
                    ->where(function ($query) use ($user, $friend) {
                        $query->where('user_id', $user->id)
                            ->where('friend_id', $friend->id);
                    })
                    ->orWhere(function ($query) use ($user, $friend) {
                        $query->where('user_id', $friend->id)
                            ->where('friend_id', $user->id);
                    })
                    ->exists();
                
                if (!$exists) {
                    // Create friend relationship with random status
                    DB::table('friends')->insert([
                        'user_id' => $user->id,
                        'friend_id' => $friend->id,
                        'status' => ['pending', 'accepted', 'rejected'][rand(0, 2)],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // First, ensure every post has at least one like
        foreach ($posts as $post) {
            // Get a random user to like this post
            $randomUser = $users->random();
            
            // Check if like already exists
            $exists = DB::table('likes')
                ->where('user_id', $randomUser->id)
                ->where('post_id', $post->id)
                ->exists();
            
            if (!$exists) {
                // Create like
                DB::table('likes')->insert([
                    'user_id' => $randomUser->id,
                    'post_id' => $post->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Then, add additional random likes
        foreach ($users as $user) {
            // Get random number of additional posts to like (between 1 and 10)
            $numberOfLikes = rand(1, 10);
            
            // Get random posts to like
            $postsToLike = $posts->random($numberOfLikes);
            
            foreach ($postsToLike as $post) {
                // Check if like already exists
                $exists = DB::table('likes')
                    ->where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->exists();
                
                if (!$exists) {
                    // Create like
                    DB::table('likes')->insert([
                        'user_id' => $user->id,
                        'post_id' => $post->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
} 
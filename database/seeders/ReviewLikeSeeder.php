<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            // 要件：各レビューに0〜3人のユーザーがいいね（自分のレビューを除く）
            $eligibleUsers = $users->where('id', '!=', $review->user_id);

            if ($eligibleUsers->isEmpty())
                continue;

            $likers = $eligibleUsers->random(rand(0, min(3, $eligibleUsers->count())));

            foreach ($likers as $liker) {
                \DB::table('review_likes')->insertOrIgnore([
                    'user_id' => $liker->id,
                    'review_id' => $review->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
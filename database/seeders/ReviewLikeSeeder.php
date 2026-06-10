<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * レビューへの「いいね」データ投入用シーダー
 */
class ReviewLikeSeeder extends Seeder
{
    /**
     * 各レビューに対してランダムに「いいね」を登録
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            // 投稿者本人以外のユーザーを抽出
            $eligibleUsers = $users->where('id', '!=', $review->user_id);

            if ($eligibleUsers->isEmpty()) {
                continue;
            }

            // 最大3人のランダムなユーザーを「いいね」するユーザーとして選出
            $count = min(3, $eligibleUsers->count());
            $likerIds = $eligibleUsers->random(rand(0, $count))->pluck('id');

            // syncWithoutDetaching を使用して重複登録を防止
            $review->likedByUsers()->syncWithoutDetaching($likerIds);
        }
    }
}
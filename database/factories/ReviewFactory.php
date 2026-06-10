<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * モデルのデフォルトの属性を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // ユーザーと書籍のリレーション生成
            'user_id' => User::factory(),
            'book_id' => Book::factory(),

            // 評価値（1〜5の整数）
            'rating' => $this->faker->numberBetween(1, 5),

            // レビュー本文
            'comment' => $this->faker->realText(200),
        ];
    }
}
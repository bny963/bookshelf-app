<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),

            // 評価値（業務要件：1〜5の整数）
            'rating' => $this->faker->numberBetween(1, 5),

            // レビュー本文（長文テキスト）
            'comment' => $this->faker->realText(200), // 日本語設定にしている場合は日本語、デフォルトは英語ベースのテキスト
        ];
    }
}
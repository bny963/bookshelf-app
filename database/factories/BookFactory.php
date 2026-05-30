<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 既存のユーザーからランダムに紐付ける（あとでシーダー側で上書きも可能）
            'user_id' => User::factory(),

            // 本のタイトル風のテキスト（1〜3単語）
            'title' => ucfirst($this->faker->words(rand(1, 3), true)),

            // 著者名
            'author' => $this->faker->name(),

            // 【重要】業務要件に合わせた「13桁の数字」の文字列。重複を防ぐため unique() を付与
            'isbn' => $this->faker->unique()->numerify('#############'),

            // 過去30年から現在までのランダムな出版日
            'published_at' => $this->faker->date('Y-m-d', 'now'),

            // 本の概要（1〜3段落の長文テキスト）
            'description' => $this->faker->paragraphs(rand(1, 3), true),

            // ダミーの画像パス風の文字列
            'image_path' => 'books/covers/' . $this->faker->uuid() . '.jpg',
        ];
    }
}
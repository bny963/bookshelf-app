<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * モデルのデフォルトの属性を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Userモデルと関連付け
            'user_id' => User::factory(),

            // タイトル（1〜3単語）
            'title' => ucfirst($this->faker->words(rand(1, 3), true)),

            // 著者名
            'author' => $this->faker->name(),

            // 13桁のユニークなISBN
            'isbn' => $this->faker->unique()->numerify('#############'),

            // 出版日（過去30年〜現在）
            'published_date' => $this->faker->dateTimeBetween('-30 years', 'now')->format('Y-m-d'),

            // 概要（1〜3段落）
            'description' => $this->faker->paragraphs(rand(1, 3), true),

            // 画像URL
            'image_url' => 'books/covers/' . $this->faker->uuid() . '.jpg',
        ];
    }
}
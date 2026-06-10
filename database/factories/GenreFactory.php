<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * モデルのデフォルトの属性を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 重複しない単語に「系」を付けてジャンル名を生成
            'name' => $this->faker->unique()->word() . '系',
        ];
    }
}
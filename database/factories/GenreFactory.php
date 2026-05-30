<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GenreFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 重複しない単語を最大100桁の範囲で生成
            'name' => $this->faker->unique()->word() . '系',
        ];
    }
}
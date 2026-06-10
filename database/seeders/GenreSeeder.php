<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

/**
 * ジャンルマスターデータ投入用シーダー
 */
class GenreSeeder extends Seeder
{
    /**
     * ジャンルデータをデータベースに投入
     *
     * @return void
     */
    public function run(): void
    {
        $genres = ['小説', 'ビジネス', '技術書', '自己啓発', 'エッセイ', '歴史', '科学', '芸術', '料理', '旅行'];

        foreach ($genres as $name) {
            // firstOrCreate を使用し、重複を防ぎつつ安全にデータを登録
            Genre::firstOrCreate(['name' => $name]);
        }
    }
}
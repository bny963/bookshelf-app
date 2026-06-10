<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * データベース初期データ投入用メインシーダー
 */
class DatabaseSeeder extends Seeder
{
    /**
     * アプリケーションのデータベースを初期化（データ投入）
     *
     * @return void
     */
    public function run(): void
    {
        // 依存関係（外部キー）を考慮した順序でシーダーを実行
        // 先に親となるデータを生成し、その後に子リレーションを生成する
        $this->call([
            UserSeeder::class,      // ユーザー（基盤データ）
            GenreSeeder::class,     // ジャンル（マスターデータ）
            BookSeeder::class,      // 書籍（ユーザーとジャンルに依存）
            ReviewSeeder::class,    // レビュー（ユーザーと書籍に依存）
            FavoriteSeeder::class,  // お気に入り（ユーザーと書籍に依存）
            ReviewLikeSeeder::class,// いいね（ユーザーとレビューに依存）
        ]);
    }
}
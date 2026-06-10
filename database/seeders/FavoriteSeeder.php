<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;

/**
 * ユーザーのお気に入り書籍データ投入用シーダー
 */
class FavoriteSeeder extends Seeder
{
    /**
     * お気に入りデータを生成・登録
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 各ユーザーに対してランダムに3〜5冊のお気に入り書籍を登録
        foreach ($users as $user) {
            $favBookIds = $books->random(rand(3, 5))->pluck('id')->toArray();

            // syncWithoutDetaching を使用し、重複を防いで関連付けを実行
            $user->favoriteBooks()->syncWithoutDetaching($favBookIds);
        }
    }
}
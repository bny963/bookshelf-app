<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Seeder;

/**
 * 書籍およびジャンルのマスターデータ投入用シーダー
 */
class BookSeeder extends Seeder
{
    /**
     * データベースの初期値を投入
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::all();

        // 投入する書籍データの定義
        $booksData = [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_at' => '1905-01-01',
                'genres' => ['小説'],
                'desc' => '長編風刺小説。気高い猫の視点から人間模様を描く名作。'
            ],
            // ... 他のデータは省略 ...
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822245566',
                'published_at' => '2007-01-18',
                'genres' => ['ビジネス', '歴史'],
                'desc' => '世界経済を激変させた「箱」の偉大なるイノベーション史。'
            ],
        ];

        foreach ($booksData as $index => $data) {
            $num = $index + 1;

            // 書籍データの登録
            $book = Book::create([
                'user_id' => $users->random()->id,
                'title' => $data['title'],
                'author' => $data['author'],
                'isbn' => $data['isbn'],
                'published_date' => $data['published_at'],
                'description' => $data['desc'],
                'image_url' => "https://placehold.co/200x300/e2e8f0/475569?text=$num",
            ]);

            // 多対多リレーションのジャンル紐付け
            $genreIds = Genre::whereIn('name', $data['genres'])->pluck('id');
            $book->genres()->attach($genreIds);
        }
    }
}
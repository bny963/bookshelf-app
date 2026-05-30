<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. 固定のジャンルマスタを作成 ---
        $genreNames = ['文学・小説', '社会・政治', 'ビジネス・経済', 'コンピュータ・IT', '趣味・実用', '雑誌', 'コミック', 'ライトノベル'];
        $genres = collect();
        foreach ($genreNames as $name) {
            $genres->push(Genre::create(['name' => $name]));
        }

        // --- 2. テスト用ユーザーを作成 ---
        // ログイン確認用に固定のユーザーを1人作成
        $testUser = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // 一律で password でログイン可能に
        ]);

        // それ以外のダミーユーザーを9人作成（合計10人）
        $users = User::factory()->count(9)->create();
        $users->push($testUser); // 全ユーザーを一つのコレクションにまとめる

        // --- 3. 書籍データを50冊作成し、各種データを紐付ける ---
        // 各書籍は、登録ユーザー（user_id）をランダムに割り振る
        $books = Book::factory()->count(50)->create([
            'user_id' => fn() => $users->random()->id,
        ]);

        // --- 4. 中間テーブルやレビューのデータを生成 ---
        $books->each(function ($book) use ($users, $genres) {

            // ① 書籍×ジャンルの紐付け（多対多）
            // 1冊につき1〜3個のジャンルをランダムに選んで紐付ける（重複しないように attach を使用）
            $randomGenres = $genres->random(rand(1, 3))->pluck('id');
            $book->genres()->attach($randomGenres);

            // ② レビューの作成
            // ランダムに選んだ数人のユーザーが、この本に対してレビューを書く
            $reviewers = $users->random(rand(0, 4)); // 0〜4件のレビューがつく
            $reviewers->each(function ($reviewer) use ($book) {
                Review::factory()->create([
                    'user_id' => $reviewer->id,
                    'book_id' => $book->id,
                ]);
            });

            // ③ お気に入りの作成（複合ユニークを考慮）
            // ランダムに選んだ数人のユーザーが、この本をお気に入り登録する
            $favoriters = $users->random(rand(0, 5));
            $favoriters->each(function ($favoriter) use ($book) {
                // Eloquentのリレーション（マイドキュメント未定義なら直接インサートか、のちほどリレーションを貼る前提）
                // ここではクエリビルダで安全にインサートします
                \DB::table('favorites')->insert([
                    'user_id' => $favoriter->id,
                    'book_id' => $book->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        });

        // --- 5. レビューへのいいねを生成（複合ユニークを考慮） ---
        $allReviews = Review::all();
        $allReviews->each(function ($review) use ($users) {
            // 1つのレビューに対して、ランダムに数人が「いいね」を押す
            $likers = $users->random(rand(0, 3));
            $likers->each(function ($liker) use ($review) {
                \DB::table('review_likes')->insert([
                    'user_id' => $liker->id,
                    'review_id' => $review->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        });
    }
}
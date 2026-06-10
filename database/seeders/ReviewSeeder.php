<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * 書籍レビューデータ投入用シーダー
 */
class ReviewSeeder extends Seeder
{
    /**
     * データベースへレビューデータを投入
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 評価値ごとのコメントテンプレート
        $comments = [
            1 => '期待外れでした...',
            2 => 'あまり好みではありませんでした。',
            3 => '可もなく不可もなく。',
            4 => 'とても参考になりました。',
            5 => '最高に素晴らしい一冊！',
        ];

        foreach ($books as $book) {
            // 各書籍に対し、2〜4人のユーザーからレビューを生成
            $reviewers = $users->random(rand(2, 4));

            foreach ($reviewers as $reviewer) {
                $rating = rand(1, 5);

                Review::create([
                    'user_id' => $reviewer->id,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'comment' => $comments[$rating],
                ]);
            }
        }
    }
}
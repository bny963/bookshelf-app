<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 評価ごとのテンプレート（5段階）
        $comments = [
            1 => '期待外れでした...',
            2 => 'あまり好みではありませんでした。',
            3 => '可もなく不可もなく。',
            4 => 'とても参考になりました。',
            5 => '最高に素晴らしい一冊！',
        ];

        foreach ($books as $book) {
            // 各書籍に2〜4件のレビューをランダムに割り当て
            $targetCount = rand(2, 4);
            $reviewers = $users->random($targetCount);

            foreach ($reviewers as $reviewer) {
                // 1〜5の全範囲に拡大
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
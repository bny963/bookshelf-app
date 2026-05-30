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

        // 要件：5人のユーザーが11冊に対して32件のレビューを投稿（各書籍に2〜4件、rating 3〜5）
        $comments = [
            5 => '非常に素晴らしい内容でした。人生のバイブルにします。',
            4 => 'とても参考になりました。読みやすくておすすめです。',
            3 => '内容は良いですが、少しボリュームが多くて読むのに時間がかかりました。',
        ];

        $reviewCount = 0;

        foreach ($books as $book) {
            // 各本に確実に2件〜4件のレビューを配分（32件になるように調整）
            // 11冊中10冊に3件、1冊に2件でちょうど32件になります
            $targetCount = ($reviewCount < 30) ? 3 : 2;

            // レビューを書くユーザーをランダムに選出
            $reviewers = $users->random($targetCount);

            foreach ($reviewers as $reviewer) {
                $rating = rand(3, 5); // 要件：ratingは3〜5の範囲

                // 要件：create を使用
                Review::create([
                    'user_id' => $reviewer->id,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'comment' => "【ユーザー: {$reviewer->name}】" . $comments[$rating],
                ]);
                $reviewCount++;
            }
        }
    }
}
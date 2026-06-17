<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 書籍レビューデータ投入用シーダー（32件固定）
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
        $comments = [
            1 => '期待外れでした...',
            2 => 'あまり好みではありませんでした。',
            3 => '可もなく不可もなく。',
            4 => 'とても参考になりました。',
            5 => '最高に素晴らしい一冊！',
        ];

        // isbn × email の組み合わせで32件を固定定義
        $reviewsData = [
            // 吾輩は猫である（3件）
            ['isbn' => '9784101010014', 'email' => 'yamada@example.com',    'rating' => 5],
            ['isbn' => '9784101010014', 'email' => 'suzuki@example.com',    'rating' => 4],
            ['isbn' => '9784101010014', 'email' => 'tanaka@example.com',    'rating' => 5],
            // 人を動かす（3件）
            ['isbn' => '9784422100524', 'email' => 'yamada@example.com',    'rating' => 4],
            ['isbn' => '9784422100524', 'email' => 'suzuki@example.com',    'rating' => 5],
            ['isbn' => '9784422100524', 'email' => 'tanaka@example.com',    'rating' => 4],
            // リーダブルコード（3件）
            ['isbn' => '9784873115658', 'email' => 'yamada@example.com',    'rating' => 5],
            ['isbn' => '9784873115658', 'email' => 'suzuki@example.com',    'rating' => 4],
            ['isbn' => '9784873115658', 'email' => 'tanaka@example.com',    'rating' => 5],
            // 7つの習慣（3件）
            ['isbn' => '9784863940246', 'email' => 'suzuki@example.com',    'rating' => 4],
            ['isbn' => '9784863940246', 'email' => 'tanaka@example.com',    'rating' => 3],
            ['isbn' => '9784863940246', 'email' => 'sato@example.com',      'rating' => 5],
            // 坊っちゃん（3件）
            ['isbn' => '9784101010021', 'email' => 'yamada@example.com',    'rating' => 4],
            ['isbn' => '9784101010021', 'email' => 'tanaka@example.com',    'rating' => 4],
            ['isbn' => '9784101010021', 'email' => 'sato@example.com',      'rating' => 3],
            // サピエンス全史（3件）
            ['isbn' => '9784309226712', 'email' => 'yamada@example.com',    'rating' => 5],
            ['isbn' => '9784309226712', 'email' => 'suzuki@example.com',    'rating' => 4],
            ['isbn' => '9784309226712', 'email' => 'sato@example.com',      'rating' => 4],
            // Clean Code（3件）
            ['isbn' => '9784048930598', 'email' => 'suzuki@example.com',    'rating' => 5],
            ['isbn' => '9784048930598', 'email' => 'tanaka@example.com',    'rating' => 4],
            ['isbn' => '9784048930598', 'email' => 'takahashi@example.com', 'rating' => 5],
            // 嫌われる勇気（3件）
            ['isbn' => '9784478025819', 'email' => 'yamada@example.com',    'rating' => 4],
            ['isbn' => '9784478025819', 'email' => 'sato@example.com',      'rating' => 5],
            ['isbn' => '9784478025819', 'email' => 'takahashi@example.com', 'rating' => 4],
            // 火花（3件）
            ['isbn' => '9784163902302', 'email' => 'suzuki@example.com',    'rating' => 3],
            ['isbn' => '9784163902302', 'email' => 'tanaka@example.com',    'rating' => 4],
            ['isbn' => '9784163902302', 'email' => 'takahashi@example.com', 'rating' => 5],
            // FACTFULNESS（3件）
            ['isbn' => '9784822289607', 'email' => 'yamada@example.com',    'rating' => 5],
            ['isbn' => '9784822289607', 'email' => 'suzuki@example.com',    'rating' => 4],
            ['isbn' => '9784822289607', 'email' => 'takahashi@example.com', 'rating' => 4],
            // コンテナ物語（2件）
            ['isbn' => '9784822245566', 'email' => 'tanaka@example.com',    'rating' => 3],
            ['isbn' => '9784822245566', 'email' => 'takahashi@example.com', 'rating' => 4],
        ];

        $userMap = User::all()->keyBy('email');
        $bookMap = Book::all()->keyBy('isbn');

        foreach ($reviewsData as $data) {
            $user = $userMap[$data['email']] ?? null;
            $book = $bookMap[$data['isbn']] ?? null;

            if (!$user || !$book) {
                continue;
            }

            Review::firstOrCreate(
                ['user_id' => $user->id, 'book_id' => $book->id],
                ['rating' => $data['rating'], 'comment' => $comments[$data['rating']]]
            );
        }
    }
}

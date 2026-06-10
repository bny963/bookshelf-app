<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * レビューモデルのリレーションシップテスト
 */
class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * レビューがユーザーと書籍に対して正しく「belongsTo」リレーションを持っていること
     */
    public function レビューはユーザーと書籍に所属している(): void
    {
        // 1. ユーザーと書籍を作成
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 2. レビューを作成して紐付ける
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 3. リレーションが正しく解決されるか検証
        $this->assertEquals($user->id, $review->user->id);
        $this->assertEquals($book->id, $review->book->id);
    }
}
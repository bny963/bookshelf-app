<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function レビューはユーザーと書籍に所属している()
    {
        // 1. ユーザーと書籍を作成
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 2. レビューを作成して紐付ける
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 3. リレーションメソッドを呼び出して検証
        $this->assertEquals($user->id, $review->user->id);
        $this->assertEquals($book->id, $review->book->id);
    }
}
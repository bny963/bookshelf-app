<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ユーザーモデルのリレーションシップテスト
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ユーザーがお気に入りの書籍と、いいねしたレビューを正しく保持できること
     */
    public function ユーザーはお気に入りの書籍といいねしたレビューを持つことができる(): void
    {
        $user = User::factory()->create();

        // 1. お気に入り書籍の紐付け (belongsToMany)
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book->id);

        // 2. いいねしたレビューの紐付け (belongsToMany)
        $review = Review::factory()->create();
        $user->likedReviews()->attach($review->id);

        // リレーションの検証
        $this->assertCount(1, $user->favoriteBooks);
        $this->assertCount(1, $user->likedReviews);
    }
}
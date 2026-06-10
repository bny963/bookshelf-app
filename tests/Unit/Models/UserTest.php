<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーはお気に入りの書籍といいねしたレビューを持つことができる()
    {
        $user = User::factory()->create();

        // 1. お気に入り書籍の紐付け (belongsToMany)
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book->id);

        // 2. いいねしたレビューの紐付け (belongsToManyなど)
        $review = Review::factory()->create();
        $user->likedReviews()->attach($review->id);

        // リレーションの検証 (ここでメソッドが呼ばれ、カバレッジが100%になります)
        $this->assertCount(1, $user->favoriteBooks);
        $this->assertCount(1, $user->likedReviews);
    }
}
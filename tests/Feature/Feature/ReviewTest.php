<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create();
    }

    /** @test */
    public function 認証済みユーザーはレビューを投稿できる()
    {
        $reviewData = [
            'rating' => 5,
            'comment' => '非常に読み応えのある素晴らしい本でした。',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('reviews.store', $this->book), $reviewData);

        $response->assertRedirect(route('books.show', $this->book));

        // データベースに投稿内容が反映されているか検証
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => $reviewData['rating'],
            'comment' => $reviewData['comment'],
        ]);
    }

    /** @test */
    public function 認証済みユーザーは自分のレビューを更新できる()
    {
        // 既存のレビューをFactoryで作成
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
        ]);

        $updatedData = [
            'rating' => 3,
            'comment' => '修正後のコメントです。',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('reviews.update', $review), $updatedData);

        $response->assertRedirect(route('books.show', $this->book));
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 3,
            'comment' => '修正後のコメントです。',
        ]);
    }

    /** @test */
    public function 他人のレビューは更新できない()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = \App\Models\Review::factory()->create(['user_id' => $owner->id]);

        // 1. AuthorizationException が投げられることを期待する
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        // 2. 実行する
        $this->actingAs($otherUser)
            ->put(route('reviews.update', $review), [
                'comment' => '勝手に更新',
                'rating' => 5,
            ]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍レビュー機能の機能テスト
 */
class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create();
    }

    /**
     * @test
     * 認証済みユーザーがレビューを新規投稿できること
     */
    public function 認証済みユーザーはレビューを投稿できる(): void
    {
        $reviewData = [
            'rating' => 5,
            'comment' => '非常に読み応えのある素晴らしい本でした。',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('reviews.store', $this->book), $reviewData);

        $response->assertRedirect(route('books.show', $this->book));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'book_id' => $this->book->id,
            'rating' => $reviewData['rating'],
            'comment' => $reviewData['comment'],
        ]);
    }

    /**
     * @test
     * 認証済みユーザーが自身のレビューを更新できること
     */
    public function 認証済みユーザーは自分のレビューを更新できる(): void
    {
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
            'rating' => $updatedData['rating'],
            'comment' => $updatedData['comment'],
        ]);
    }

    /**
     * @test
     * 他人のレビューの更新リクエストは認可エラー（AuthorizationException）になること
     */
    public function 他人のレビューは更新できない(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id]);

        $this->expectException(AuthorizationException::class);

        $this->actingAs($otherUser)
            ->put(route('reviews.update', $review), [
                'comment' => '勝手に更新',
                'rating' => 5,
            ]);
    }
}
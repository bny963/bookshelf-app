<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍更新リクエストのバリデーションテスト
 */
class BookUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 不正なデータ（空のタイトルなど）で更新リクエストを送信した場合、
     * バリデーションエラー（422）が返却されること
     */
    public function 不正なデータで更新しようとするとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->withExceptionHandling();

        // 必須項目が欠落した不正なデータを送信
        $response = $this->actingAs($user)
            ->patchJson("/api/v1/books/{$book->id}", [
                'title' => '',
            ]);

        // 期待されるバリデーションエラー項目の検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'genres']);
    }
}
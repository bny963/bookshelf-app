<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // キャッシュを強制的にクリアしてからテスト開始
        $this->artisan('route:clear');
    }
    use RefreshDatabase;

    /** @test */
    public function ユーザーはお気に入りを登録・解除できる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 1. お気に入り登録
        $this->actingAs($user)
            ->post(route('favorites.store', $book));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 2. お気に入り解除
        $this->actingAs($user)
            ->delete(route('favorites.destroy', $book));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * お気に入り機能のテストクラス
 */
class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // ルートキャッシュをクリアしてテスト環境をクリーンに保つ
        $this->artisan('route:clear');
    }

    /**
     * @test
     * ユーザーがお気に入りの登録と解除を正常に行えること
     */
    public function ユーザーはお気に入りを登録・解除できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 1. お気に入り登録の検証
        $this->actingAs($user)
            ->post(route('favorites.store', $book));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // 2. お気に入り解除の検証
        $this->actingAs($user)
            ->delete(route('favorites.destroy', $book));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
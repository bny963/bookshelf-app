<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍のソート機能のテストクラス
 */
class SortTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ソート条件（最新順・古い順）によって書籍一覧の並び順が正しく変わること
     */
    public function books_can_be_sorted_by_multiple_criteria(): void
    {
        $user = User::factory()->create();

        Book::factory()->create(['title' => 'Aタイトル', 'created_at' => now()->subDays(20)]);
        Book::factory()->create(['title' => 'Bタイトル', 'created_at' => now()->subDays(1)]);

        // 最新順の検証
        $this->actingAs($user)
            ->getJson('/api/v1/books?sort=latest')
            ->assertJsonPath('data.0.title', 'Bタイトル');

        // 古い順の検証
        $this->actingAs($user)
            ->getJson('/api/v1/books?sort=oldest')
            ->assertJsonPath('data.0.title', 'Aタイトル');
    }

    /**
     * @test
     * フィルタリングとソートが併用された場合でもAPIが正常に動作すること
     */
    public function sorting_persists_with_filter(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);

        $response = $this->actingAs($user)
            ->getJson(route('api.v1.books.index', [
                'genre_id' => $genre->id,
                'sort' => 'latest'
            ]));

        $response->assertStatus(200);
    }
}
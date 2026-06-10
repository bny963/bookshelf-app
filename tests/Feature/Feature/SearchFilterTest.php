<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト用のデータ準備
        $this->genre = Genre::create(['name' => '技術書']);
        $this->book = Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        $this->book->genres()->attach($this->genre->id);
    }

    /** @test */
    public function filter_by_keyword_and_genre()
    {
        $user = \App\Models\User::factory()->create(); // ユーザー生成

        // 全ての行で actingAs($user) をチェーンさせます
        $this->actingAs($user)
            ->getJson('/api/v1/books?keyword=Laravel')
            ->assertJsonPath('meta.total', 1);

        $this->actingAs($user)
            ->getJson('/api/v1/books?keyword=存在しない本') // ※「絶対に存在しないキーワード」推奨
            ->assertJsonPath('meta.total', 0);

        $this->actingAs($user)
            ->getJson('/api/v1/books?genre_id=' . $this->genre->id)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_pagination_maintains_query_string()
    {
        // ページネーション動作確認
        // ページ遷移時に keyword が URL に含まれているかを確認
        $response = $this->getJson('/api/v1/books?keyword=Laravel&page=2');

        $response->assertSee('keyword=Laravel');
    }
}
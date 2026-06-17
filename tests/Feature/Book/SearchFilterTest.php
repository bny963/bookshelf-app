<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍の検索・フィルタリング機能のテストクラス
 */
class SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    protected Genre $genre;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト用のデータ準備
        $this->genre = Genre::create(['name' => '技術書']);
        $this->book = Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        $this->book->genres()->attach($this->genre->id);
    }

    /**
     * @test
     * キーワードおよびジャンルIDによるフィルタリングが正しく動作すること
     */
    public function filter_by_keyword_and_genre(): void
    {
        $user = User::factory()->create();

        // 1. キーワード検索の確認
        $this->actingAs($user)
            ->getJson('/api/v1/books?keyword=Laravel')
            ->assertJsonPath('meta.total', 1);

        // 2. 存在しないキーワードでの検索確認
        $this->actingAs($user)
            ->getJson('/api/v1/books?keyword=存在しない本')
            ->assertJsonPath('meta.total', 0);

        // 3. ジャンルIDによるフィルタリング確認
        $this->actingAs($user)
            ->getJson('/api/v1/books?genre_id=' . $this->genre->id)
            ->assertJsonPath('meta.total', 1);
    }

    /**
     * @test
     * ページネーション遷移時にもクエリパラメータが維持されること
     */
    public function test_pagination_maintains_query_string(): void
    {
        $response = $this->getJson('/api/v1/books?keyword=Laravel&page=2');

        // URLパラメータが維持されていることを確認
        $response->assertSee('keyword=Laravel');
    }
}
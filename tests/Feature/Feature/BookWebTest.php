<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍関連のWeb表示・操作の機能テスト
 */
class BookWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 書籍一覧画面が正しく表示されること
     */
    public function 一覧画面が表示される(): void
    {
        $response = $this->get('/books');

        $response->assertStatus(200)
            ->assertViewIs('books.index');
    }

    /**
     * @test
     * 認証済みユーザーが書籍登録画面にアクセスできること
     */
    public function 登録画面が表示される(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/create');

        $response->assertStatus(200)
            ->assertViewIs('books.create');
    }

    /**
     * @test
     * 認証済みユーザーが書籍データを新規登録できること
     */
    public function 書籍を登録できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $this->actingAs($user)->post('/books', [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-06-10',
            'genres' => [$genre->id],
        ]);

        $this->assertDatabaseHas('books', ['title' => 'テストタイトル']);
    }
}
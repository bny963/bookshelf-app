<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookWebTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 一覧画面が表示される()
    {
        $response = $this->get('/books');
        $response->assertStatus(200)
            ->assertViewIs('books.index');
    }

    /** @test */
    public function 登録画面が表示される()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/books/create');
        $response->assertStatus(200)
            ->assertViewIs('books.create');
    }

    /** @test */
    public function 書籍を登録できる()
    {
        $user = User::factory()->create();

        // 解決策: テスト用のジャンルを先に作成する
        $genre = \App\Models\Genre::factory()->create();

        $this->actingAs($user)->post('/books', [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'genres' => [$genre->id], // 存在するIDを渡す
        ]);

        $this->assertDatabaseHas('books', ['title' => 'テストタイトル']);
    }
}
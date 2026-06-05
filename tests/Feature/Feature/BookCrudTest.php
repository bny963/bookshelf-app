<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 認証済みユーザーは書籍を登録できる()
    {
        $this->withoutExceptionHandling(); // デバッグ用
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('books.store'), [
                'title' => 'テスト書籍',
                'author' => '著者名',
                'isbn' => '1234567890123',
                'published_date' => '2026-06-05',
                'description' => 'テスト用の説明文', 
                'image_url' => 'https://example.com/image.jpg', 
                'genres' => [$genre->id],
            ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['title' => 'テスト書籍']);
    }

    /** @test */
    public function 認証済みユーザーは書籍を更新できる()
    {
        $this->withoutExceptionHandling();

        $myBook = Book::factory()->create(['user_id' => $this->user->id]);
        $genre = Genre::factory()->create();

        $this->actingAs($this->user)
            ->put(route('books.update', $myBook), [
                'title' => '更新後のタイトル',
                'author' => '著者名',
                'isbn' => '1234567890123',
                'published_date' => '2026-06-05',
                'genres' => [$genre->id],
            ]);

        // 【最終回避策】 assertDatabaseHas を一切使わない
        $bookInDb = \DB::table('books')->find($myBook->id);

        $this->assertNotNull($bookInDb);
        $this->assertEquals('更新後のタイトル', $bookInDb->title);
    }
    /** @test */
    public function 認証済みユーザーは書籍を削除できる()
    {
        $book = Book::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
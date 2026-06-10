<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function 一覧画面を表示できる()
    {
        $this->actingAs($this->user)
            ->get(route('books.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function 登録画面を表示できる()
    {
        $this->actingAs($this->user)
            ->get(route('books.create'))
            ->assertStatus(200);
    }

    /** @test */
    public function 書籍を保存できる()
    {
        $genres = \App\Models\Genre::factory()->count(2)->create();

        $bookData = Book::factory()->make([
            'image_url' => 'https://example.com/cover.jpg', 
            'genres' => $genres->pluck('id')->toArray(),   
        ])->toArray();

        $this->actingAs($this->user)
            ->post(route('books.store'), $bookData)
            ->assertRedirect(route('books.index'));

        // データベースに登録されているか確認
        $this->assertDatabaseHas('books', ['title' => $bookData['title']]);
    }
    /** @test */
    public function 書籍詳細を表示できる()
    {
        $book = Book::factory()->create();
        $this->actingAs($this->user)
            ->get(route('books.show', $book))
            ->assertStatus(200);
    }

    /** @test */
    public function 書籍を削除できる()
    {
        $book = Book::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('books.destroy', $book))
            ->assertRedirect(route('books.index'));
    }
    /** @test */
    public function ISBN検索で書籍情報が取得できる()
    {
        // APIのレスポンスを偽装
        \Illuminate\Support\Facades\Http::fake([
            'googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'items' => [['volumeInfo' => ['title' => 'テストタイトル']]]
            ], 200)
        ]);

        $this->actingAs($this->user)
            ->getJson(route('books.isbnSearch', ['isbn' => '1234567890123']))
            ->assertStatus(200)
            ->assertJson(['title' => 'テストタイトル']);
    }
    /** @test */
    public function 一覧画面でキーワード検索とジャンル絞り込みができる()
    {
        $genre = \App\Models\Genre::factory()->create();
        $book = Book::factory()->create(['title' => 'Laravelの教科書']);
        $book->genres()->attach($genre->id);

        $this->actingAs($this->user)
            ->get(route('books.index', ['keyword' => 'Laravel', 'genre' => $genre->id]))
            ->assertStatus(200)
            ->assertSee('Laravelの教科書');
    }

    /** @test */
    public function 一覧画面でランキング順などの並び替えができる()
    {
        $this->actingAs($this->user)
            ->get(route('books.index', ['sort' => 'oldest']))
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->get(route('books.index', ['sort' => 'title']))
            ->assertStatus(200);
    }
    /** @test */
    public function 他人の書籍を削除しようとすると403エラーになる()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->withoutExceptionHandling();

        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);

        $this->actingAs($other)
            ->delete(route('books.destroy', $book));
    }

    /** @test */
    public function 存在しないISBNを検索すると404エラーになる()
    {
        \Illuminate\Support\Facades\Http::fake([
            'googleapis.com/*' => \Illuminate\Support\Facades\Http::response([], 404)
        ]);

        $this->actingAs($this->user)
            ->getJson(route('books.isbnSearch', ['isbn' => '0000000000000']))
            ->assertStatus(404); 
    }
}
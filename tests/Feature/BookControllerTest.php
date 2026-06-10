<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * 書籍管理機能の機能テスト
 */
class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * @test
     * 一覧画面が正しく表示されること
     */
    public function 一覧画面を表示できる(): void
    {
        $this->actingAs($this->user)
            ->get(route('books.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     * 登録画面が正しく表示されること
     */
    public function 登録画面を表示できる(): void
    {
        $this->actingAs($this->user)
            ->get(route('books.create'))
            ->assertStatus(200);
    }

    /**
     * @test
     * 書籍データがデータベースに正しく保存されること
     */
    public function 書籍を保存できる(): void
    {
        $genres = Genre::factory()->count(2)->create();

        $bookData = Book::factory()->make([
            'image_url' => 'https://example.com/cover.jpg',
            'genres' => $genres->pluck('id')->toArray(),
        ])->toArray();

        $this->actingAs($this->user)
            ->post(route('books.store'), $bookData)
            ->assertRedirect(route('books.index'));

        $this->assertDatabaseHas('books', ['title' => $bookData['title']]);
    }

    /**
     * @test
     * 書籍詳細画面が正しく表示されること
     */
    public function 書籍詳細を表示できる(): void
    {
        $book = Book::factory()->create();

        $this->actingAs($this->user)
            ->get(route('books.show', $book))
            ->assertStatus(200);
    }

    /**
     * @test
     * 所有している書籍が削除できること
     */
    public function 書籍を削除できる(): void
    {
        $book = Book::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('books.destroy', $book))
            ->assertRedirect(route('books.index'));
    }

    /**
     * @test
     * 外部APIを使用したISBN検索が成功すること
     */
    public function ISBN検索で書籍情報が取得できる(): void
    {
        Http::fake([
            'googleapis.com/*' => Http::response([
                'items' => [['volumeInfo' => ['title' => 'テストタイトル']]]
            ], 200)
        ]);

        $this->actingAs($this->user)
            ->getJson(route('books.isbnSearch', ['isbn' => '1234567890123']))
            ->assertStatus(200)
            ->assertJson(['title' => 'テストタイトル']);
    }

    /**
     * @test
     * 検索キーワードとジャンル絞り込みが機能すること
     */
    public function 一覧画面でキーワード検索とジャンル絞り込みができる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['title' => 'Laravelの教科書']);
        $book->genres()->attach($genre->id);

        $this->actingAs($this->user)
            ->get(route('books.index', ['keyword' => 'Laravel', 'genre' => $genre->id]))
            ->assertStatus(200)
            ->assertSee('Laravelの教科書');
    }

    /**
     * @test
     * 並び替えオプションが正しく動作すること
     */
    public function 一覧画面でランキング順などの並び替えができる(): void
    {
        $this->actingAs($this->user)
            ->get(route('books.index', ['sort' => 'oldest']))
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->get(route('books.index', ['sort' => 'title']))
            ->assertStatus(200);
    }

    /**
     * @test
     * 他人の書籍への削除権限がないこと
     */
    public function 他人の書籍を削除しようとすると403エラーになる(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->withoutExceptionHandling();
        $this->expectException(AuthorizationException::class);

        $this->actingAs($other)
            ->delete(route('books.destroy', $book));
    }

    /**
     * @test
     * API検索で存在しないISBNを指定した際に404エラーになること
     */
    public function 存在しないISBNを検索すると404エラーになる(): void
    {
        Http::fake([
            'googleapis.com/*' => Http::response([], 404)
        ]);

        $this->actingAs($this->user)
            ->getJson(route('books.isbnSearch', ['isbn' => '0000000000000']))
            ->assertStatus(404);
    }
}
<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * 書籍CRUDおよび権限の機能テスト
 */
class BookCrudTest extends TestCase
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
     * 認証済みユーザーが書籍を新規登録できること
     */
    public function 認証済みユーザーは書籍を登録できる(): void
    {
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

    /**
     * @test
     * 認証済みユーザーが自身の書籍を更新できること
     */
    public function 認証済みユーザーは書籍を更新できる(): void
    {
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

        $bookInDb = DB::table('books')->find($myBook->id);
        $this->assertNotNull($bookInDb);
        $this->assertEquals('更新後のタイトル', $bookInDb->title);
    }

    /**
     * @test
     * 認証済みユーザーが書籍を削除できること
     */
    public function 認証済みユーザーは書籍を削除できる(): void
    {
        $book = Book::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('books.destroy', $book));

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * @test
     * API経由での書籍登録ができること
     */
    public function test_user_can_create_book(): void
    {
        $genre = Genre::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => '著者名',
            'isbn' => '9784101010014',
            'genres' => [$genre->id]
        ];

        $this->actingAs($this->user)
            ->postJson('/api/v1/books', $data)
            ->assertStatus(201);
    }

    /**
     * @test
     * 他人の書籍の更新には権限エラー（403）が返ること
     */
    public function test_book_update_policy_authorization(): void
    {
        $other = User::factory()->create();
        $genre = Genre::factory()->create();
        $myBook = Book::factory()->create(['user_id' => $this->user->id]);
        $othersBook = Book::factory()->create(['user_id' => $other->id]);

        $updateData = ['title' => '権限チェック', 'author' => '著者', 'genres' => [$genre->id]];

        // 自分の本は更新可能
        $this->actingAs($this->user, 'sanctum')
            ->patchJson("/api/v1/books/{$myBook->id}", $updateData)
            ->assertStatus(200);

        // 他人の本は更新不可
        $this->actingAs($this->user, 'sanctum')
            ->withExceptionHandling()
            ->patchJson("/api/v1/books/{$othersBook->id}", $updateData)
            ->assertStatus(403);
    }

    /**
     * @test
     * 他人の書籍の削除には権限エラー（403）が返ること
     */
    public function test_book_delete_policy_authorization(): void
    {
        $othersBook = Book::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs($this->user)
            ->withExceptionHandling()
            ->deleteJson("/api/v1/books/{$othersBook->id}")
            ->assertStatus(403);
    }
}
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

    // 作成成功のテスト
    public function test_user_can_create_book()
    {
        // ジャンルを先に作成しておく
        $genre = Genre::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => '著者名',
            'isbn' => '9784101010014',
            'genres' => [$genre->id] // ここで作成したIDを渡す
        ];

        $this->actingAs($this->user)
            ->postJson('/api/v1/books', $data)
            ->assertStatus(201);
    }

    // 更新の認可テスト（自分 vs 他人）
    public function test_book_update_policy_authorization()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $genre = Genre::factory()->create();

        $myBook = Book::factory()->create(['user_id' => $me->id]);
        $othersBook = Book::factory()->create(['user_id' => $other->id]);

        $updateData = [
            'title' => '権限チェック',
            'author' => 'テスト著者',
            'genres' => [$genre->id]
        ];

        $this->actingAs($me, 'sanctum')
            ->patchJson("/api/v1/books/{$myBook->id}", $updateData)
            ->assertStatus(200);

        $response = $this->actingAs($me, 'sanctum')
            ->withExceptionHandling()
            ->patchJson("/api/v1/books/{$othersBook->id}", $updateData);

        $response->assertStatus(403);
    }
    // 削除の認可テスト
    public function test_book_delete_policy_authorization()
    {
        $othersBook = Book::factory()->create(['user_id' => User::factory()->create()->id]);

        // 認可エラーを例外としてではなくレスポンスとして取得する
        $response = $this->actingAs($this->user)
            ->withExceptionHandling() // 追加
            ->deleteJson("/api/v1/books/{$othersBook->id}");

        $response->assertStatus(403);
    }
}
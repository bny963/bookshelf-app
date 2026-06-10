<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Genre;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 同じ名前のジャンルは登録できない()
    {
        $user = User::factory()->create();
        $name = 'SF系_' . uniqid();

        Genre::factory()->create(['name' => $name]);

        // 重要: withExceptionHandling を明示的に呼んでバリデーションエラーをレスポンスに変換させる
        $response = $this->actingAs($user)
            ->withExceptionHandling()
            ->postJson(route('genres.store'), ['name' => $name]);

        // これで 422 が返ってくるはずです
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
    /** @test */
    public function 自分のIDを除外して更新できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '小説系']);

        // 名前を変更せず、そのまま更新リクエストを送る（一意性エラーにならないことを確認）
        $response = $this->actingAs($user)
            ->put(route('genres.update', $genre), [
                'name' => '小説系',
            ]);

        $response->assertRedirect(route('genres.index'));
    }
    /** @test */
    public function ジャンルは書籍とリレーションを持つことができる()
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $genre->books()->attach($book->id);

        $this->assertCount(1, $genre->books);
        $this->assertEquals($book->id, $genre->books->first()->id);
    }
}
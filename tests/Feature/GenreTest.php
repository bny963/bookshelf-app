<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ジャンル機能のユニットおよび機能テスト
 */
class GenreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 同一名称のジャンル登録がバリデーションにより拒否されること
     */
    public function 同じ名前のジャンルは登録できない(): void
    {
        $user = User::factory()->create();
        $name = 'SF系_' . uniqid();

        Genre::factory()->create(['name' => $name]);

        $response = $this->actingAs($user)
            ->withExceptionHandling()
            ->postJson(route('genres.store'), ['name' => $name]);

        // バリデーションエラー（422）の検証
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * @test
     * 既存ジャンルの更新時、自分自身の名称であれば一意性チェックに抵触しないこと
     */
    public function 自分のIDを除外して更新できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '小説系']);

        // 名前を変更せず、同じ名称で更新リクエストを送信
        $response = $this->actingAs($user)
            ->put(route('genres.update', $genre), [
                'name' => '小説系',
            ]);

        $response->assertRedirect(route('genres.index'));
    }

    /**
     * @test
     * ジャンルと書籍が多対多リレーションで正しく関連付けられること
     */
    public function ジャンルは書籍とリレーションを持つことができる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        // 関連付けの実行
        $genre->books()->attach($book->id);

        $this->assertCount(1, $genre->books);
        $this->assertEquals($book->id, $genre->books->first()->id);
    }
}
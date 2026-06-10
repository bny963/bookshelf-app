<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function APIから書籍一覧が正しい形式で取得できる()
    {
        $user = \App\Models\User::factory()->create();

        Book::factory()->count(5)->create();

        // actingAs($user) を追加
        $response = $this->actingAs($user)
            ->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'isbn']
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function 必須項目が欠けているとエラーが返る()
    {
        $user = User::factory()->create();

        // 1. バリデーションエラーが起きるリクエストを投げる
        // 2. 例外をテストケース側でキャッチするために、withExceptionHandling() を明示的に呼ぶ（デフォルトですが念のため）
        $response = $this->actingAs($user)
            ->withExceptionHandling()
            ->postJson('/api/v1/books', []);

        // 3. 422が返ることをアサートし、これでバリデーションエラーを「テスト成功」として扱う
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'genres']);
    }
}
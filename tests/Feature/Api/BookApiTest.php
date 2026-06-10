<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 書籍関連APIの機能テスト
 */
class BookApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 書籍一覧取得APIが正しい形式のJSONを返却することを確認
     */
    public function APIから書籍一覧が正しい形式で取得できる(): void
    {
        $user = User::factory()->create();
        Book::factory()->count(5)->create();

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

    /**
     * @test
     * バリデーションルールに違反した場合、適切なエラーレスポンスが返却されること
     */
    public function 必須項目が欠けているとエラーが返る(): void
    {
        $user = User::factory()->create();

        // 空のリクエストを送信してバリデーションエラーを検証
        $response = $this->actingAs($user)
            ->withExceptionHandling()
            ->postJson('/api/v1/books', []);

        // ステータスコード422（Unprocessable Entity）とバリデーションエラーの確認
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'genres']);
    }
}
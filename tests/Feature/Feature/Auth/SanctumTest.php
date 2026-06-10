<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Laravel Sanctum 認証の機能テスト
 */
class SanctumTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 未認証ユーザーが保護されたAPIにアクセスした場合、401エラーになることの確認
     */
    public function test_unauthenticated_user_gets_401(): void
    {
        // 例外をハンドリングせずにそのままスローさせ、AuthenticationExceptionを検証
        $this->withoutExceptionHandling();
        $this->expectException(AuthenticationException::class);

        $this->postJson('/api/v1/books', []);
    }

    /**
     * @test
     * 認証済みユーザーが有効なトークンでアクセスした場合、200が返ることの確認
     */
    public function test_authenticated_user_gets_200(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // 生成したトークンを使用してリクエストを送信
        $this->withToken($token)
            ->getJson('/api/v1/books')
            ->assertStatus(200);
    }
}
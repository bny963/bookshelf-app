<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * 認証機能のテストクラス
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインページが正しく表示されること
     */
    #[Test]
    public function ユーザーはログイン画面にアクセスできる(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * 正しい資格情報でWebログインが成功し、リダイレクトされること
     */
    #[Test]
    public function 正しいパスワードでログインできる(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 新規ユーザー登録が正常に行われ、DBに保存されること
     */
    #[Test]
    public function ユーザー登録ができる(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /**
     * API経由で認証情報が正しければログイン成功すること
     */
    #[Test]
    public function ログインができる(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertStatus(200);
    }
}
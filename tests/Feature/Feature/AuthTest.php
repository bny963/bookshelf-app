<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーはログイン画面にアクセスできる()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function 正しいパスワードでログインできる()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/'); // またはダッシュボードなど
        $this->assertAuthenticatedAs($user);
    }
}
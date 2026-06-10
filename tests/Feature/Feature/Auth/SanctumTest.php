<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_unauthenticated_user_gets_401()
    {
        // このテストメソッド内で AuthenticationException が投げられることを期待する
        $this->withoutExceptionHandling();
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);

        $this->postJson('/api/v1/books', []);
    }
    /** @test */
    public function test_authenticated_user_gets_200()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // トークン付きでアクセス
        $this->withToken($token)
            ->getJson('/api/v1/books')
            ->assertStatus(200);
    }
}
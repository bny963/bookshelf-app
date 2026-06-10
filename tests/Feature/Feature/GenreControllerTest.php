<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーはジャンルを登録できる()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/genres', ['name' => 'テストジャンル']);

        $response->assertStatus(302); // Webコントローラーなのでリダイレクト確認
        $this->assertDatabaseHas('genres', ['name' => 'テストジャンル']);
    }
    /** @test */
    public function ジャンル一覧を表示できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('genres.index'))
            ->assertStatus(200);
    }

    /** @test */
    public function ジャンルを登録できる()
    {
        $user = User::factory()->create();
        $genreData = ['name' => '新しいジャンル'];

        $this->actingAs($user)
            ->post(route('genres.store'), $genreData)
            ->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', ['name' => '新しいジャンル']);
    }

    /** @test */
    public function ジャンルを削除できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $this->actingAs($user)
            ->delete(route('genres.destroy', $genre))
            ->assertRedirect(route('genres.index'));

        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}
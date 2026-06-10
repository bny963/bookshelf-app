<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ジャンル管理機能の機能テスト
 */
class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 認証済みユーザーがジャンルを登録できること
     */
    public function 認証済みユーザーはジャンルを登録できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/genres', ['name' => 'テストジャンル']);

        // リダイレクトを確認
        $response->assertStatus(302);
        $this->assertDatabaseHas('genres', ['name' => 'テストジャンル']);
    }

    /**
     * @test
     * ジャンル一覧画面が正しく表示されること
     */
    public function ジャンル一覧を表示できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('genres.index'))
            ->assertStatus(200);
    }

    /**
     * @test
     * ジャンルが正しく保存され、一覧画面へリダイレクトされること
     */
    public function ジャンルを登録できる(): void
    {
        $user = User::factory()->create();
        $genreData = ['name' => '新しいジャンル'];

        $this->actingAs($user)
            ->post(route('genres.store'), $genreData)
            ->assertRedirect(route('genres.index'));

        $this->assertDatabaseHas('genres', ['name' => '新しいジャンル']);
    }

    /**
     * @test
     * 既存のジャンルが正しく削除されること
     */
    public function ジャンルを削除できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $this->actingAs($user)
            ->delete(route('genres.destroy', $genre))
            ->assertRedirect(route('genres.index'));

        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}
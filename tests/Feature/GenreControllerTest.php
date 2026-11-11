<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * ジャンル管理機能の機能テスト
 */
class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証済みユーザーがジャンルを登録できること
     */
    #[Test]
    public function 認証済みユーザーはジャンルを登録できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/genres', ['name' => 'テストジャンル']);

        // リダイレクトを確認
        $response->assertStatus(302);
        $this->assertDatabaseHas('genres', ['name' => 'テストジャンル']);
    }

    /**
     * ジャンル一覧画面が正しく表示されること
     */
    #[Test]
    public function ジャンル一覧を表示できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('genres.index'))
            ->assertStatus(200);
    }

    /**
     * ジャンルが正しく保存され、一覧画面へリダイレクトされること
     */
    #[Test]
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
     * 既存のジャンルが正しく削除されること
     */
    #[Test]
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
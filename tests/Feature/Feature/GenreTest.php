<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 同じ名前のジャンルは登録できない()
    {
        $user = User::factory()->create();

        // 1. 既存のジャンルを作成
        $existingGenre = Genre::factory()->create(['name' => 'SF系']);

        // 2. 同じ名前でPOST
        $response = $this->actingAs($user)
            ->post(route('genres.store'), [
                'name' => 'SF系',
            ]);

        // 3. バリデーションエラーが返ることを確認
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('genres', 1);
    }

    /** @test */
    public function 自分のIDを除外して更新できる()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '小説系']);

        // 名前を変更せず、そのまま更新リクエストを送る（一意性エラーにならないことを確認）
        $response = $this->actingAs($user)
            ->put(route('genres.update', $genre), [
                'name' => '小説系',
            ]);

        $response->assertRedirect(route('genres.index'));
    }
}
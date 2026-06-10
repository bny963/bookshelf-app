<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Genre;

class SortTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function books_can_be_sorted_by_multiple_criteria()
    {
        $user = \App\Models\User::factory()->create(); // ユーザー生成

        Book::factory()->create(['title' => 'Aタイトル', 'created_at' => now()->subDays(20)]);
        Book::factory()->create(['title' => 'Bタイトル', 'created_at' => now()->subDays(1)]);

        $this->actingAs($user) // 追加
            ->getJson('/api/v1/books?sort=latest')
            ->assertJsonPath('data.0.title', 'Bタイトル');

        $this->actingAs($user) // 追加
            ->getJson('/api/v1/books?sort=oldest')
            ->assertJsonPath('data.0.title', 'Aタイトル');
    }

    /** @test */
    public function sorting_persists_with_filter()
    {
        $user = \App\Models\User::factory()->create(); // ユーザー生成
        $genre = Genre::factory()->create();

        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);

        // actingAs($user) を追加
        $response = $this->actingAs($user)
            ->getJson(route('api.v1.books.index', [
                'genre_id' => $genre->id,
                'sort' => 'latest'
            ]));

        $response->assertStatus(200);
    }
}
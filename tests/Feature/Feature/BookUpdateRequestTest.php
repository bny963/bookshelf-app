<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 不正なデータで更新しようとするとバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->withExceptionHandling();

        $response = $this->actingAs($user)
            ->patchJson("/api/v1/books/{$book->id}", [
                'title' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'genres']);
    }
}
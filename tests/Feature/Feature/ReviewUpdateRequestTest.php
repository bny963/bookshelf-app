<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 不正なデータでレビュー更新しようとするとバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->withoutExceptionHandling([
            \Illuminate\Validation\ValidationException::class,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/reviews/{$review->id}", [
                'rating' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function 正しいデータなら更新できる()
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->putJson("/reviews/{$review->id}", [
                'comment' => 'テストコメント',
                'rating' => 5,
            ]);

        $response->assertStatus(302); // またはAPIなら200
    }
}
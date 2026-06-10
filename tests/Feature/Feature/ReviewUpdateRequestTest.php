<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * レビュー更新リクエストのバリデーションテスト
 */
class ReviewUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 不正なデータ（空の評価など）で更新リクエストを送信した場合、
     * バリデーションエラー（422）が返却されること
     */
    public function 不正なデータでレビュー更新しようとするとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $this->withoutExceptionHandling([ValidationException::class]);

        $response = $this->actingAs($user)
            ->putJson("/reviews/{$review->id}", [
                'rating' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    /**
     * @test
     * 正しいデータで更新リクエストを送信した場合、更新が成功すること
     */
    public function 正しいデータなら更新できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->putJson("/reviews/{$review->id}", [
                'comment' => 'テストコメント',
                'rating' => 5,
            ]);

        $response->assertStatus(302);
    }
}
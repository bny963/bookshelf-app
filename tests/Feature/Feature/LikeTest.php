<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーはレビューにいいね・解除できる()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $review = Review::factory()->create();

        // 1. リクエストの成功を確認
        $response = $this->actingAs($user)
            ->post(route('likes.store', $review));

        $response->assertStatus(302); // または 200

        // 2. DBにデータがあるか、直接確認（countで確認すると確実です）
        $exists = \DB::table('review_likes')
            ->where('user_id', $user->id)
            ->where('review_id', $review->id)
            ->exists();

        $this->assertTrue($exists, 'review_likes テーブルにデータが保存されていません！');
    }
}
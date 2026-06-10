<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * レビューの「いいね」機能のテストクラス
 */
class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * ユーザーがレビューにいいねをし、正しくデータベースに記録されること
     */
    public function ユーザーはレビューにいいね・解除できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        // 1. 「いいね」登録リクエスト
        $response = $this->actingAs($user)
            ->post(route('likes.store', $review));

        $response->assertStatus(302); // リダイレクト（または200）

        // 2. データベースへの保存確認
        $exists = DB::table('review_likes')
            ->where('user_id', $user->id)
            ->where('review_id', $review->id)
            ->exists();

        $this->assertTrue($exists, 'review_likes テーブルにデータが保存されていません！');
    }
}
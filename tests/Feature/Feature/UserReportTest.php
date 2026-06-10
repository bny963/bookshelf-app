<?php

namespace Tests\Feature\Report;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ユーザー読書レポート機能のテストクラス
 */
class UserReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 認証済みユーザーが自身の読書レポート（レビュー数）を確認できること
     */
    public function test_user_can_see_their_own_reading_report(): void
    {
        $user = User::factory()->create();

        // 2件のレビューを作成
        Review::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/report');

        $response->assertStatus(200)
            ->assertJsonPath('summary.total_reviews', 2);
    }

    /**
     * @test
     * レビューデータが存在しない場合でもエラーにならず正常なレスポンスが返ること
     */
    public function test_report_handles_empty_data_gracefully(): void
    {
        $user = User::factory()->create();

        // レビューが0件の状態でレポートAPIにアクセス
        $response = $this->actingAs($user)->getJson('/api/v1/report');

        // エラーにならず集計値0が返ることを確認
        $response->assertStatus(200)
            ->assertJsonPath('summary.total_reviews', 0);
    }
}
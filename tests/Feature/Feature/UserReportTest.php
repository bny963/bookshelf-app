<?php

namespace Tests\Feature\Report;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserReportTest extends TestCase
{
    use RefreshDatabase;

public function test_user_can_see_their_own_reading_report()
{
    $user = User::factory()->create();

    \App\Models\Review::factory()->count(2)->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->getJson('/api/v1/report');

    $response->assertStatus(200)
             ->assertJsonPath('summary.total_reviews', 2); 
}

public function test_report_handles_empty_data_gracefully()
    {
        $user = User::factory()->create();

        // 本が0冊の状態でレポートにアクセス
        $response = $this->actingAs($user)->getJson('/api/v1/report');

        // エラーにならず、0が返ってくることを確認
        $response->assertStatus(200)
                 ->assertJsonPath('summary.total_reviews', 0); // ここを0に修正
    }
}
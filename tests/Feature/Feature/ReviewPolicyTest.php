<?php

namespace Tests\Feature\Policies;

use App\Models\Review;
use App\Models\User;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function レビューの更新と削除の権限を判定できる()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $policy = new ReviewPolicy();

        // 所有者なら true
        $this->assertTrue($policy->update($user, $review));
        // 所有者なら true
        $this->assertTrue($policy->delete($user, $review));

        // 他人なら false
        $this->assertFalse($policy->update($otherUser, $review));
        $this->assertFalse($policy->delete($otherUser, $review));
    }
}
<?php

namespace Tests\Feature\Policies;

use App\Models\Review;
use App\Models\User;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * レビューの認可ポリシー（ReviewPolicy）のテストクラス
 */
class ReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * レビュー所有者には更新・削除権限があり、他人にはないことを確認
     */
    public function レビューの更新と削除の権限を判定できる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $policy = new ReviewPolicy();

        // 所有者による操作の検証
        $this->assertTrue($policy->update($user, $review));
        $this->assertTrue($policy->delete($user, $review));

        // 他人による操作の検証
        $this->assertFalse($policy->update($otherUser, $review));
        $this->assertFalse($policy->delete($otherUser, $review));
    }
}
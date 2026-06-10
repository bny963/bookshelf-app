<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

/**
 * レビューモデルの認可ポリシー
 */
class ReviewPolicy
{
    /**
     * レビューの更新が可能か判定（投稿者のみ許可）
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * レビューの削除が可能か判定（投稿者のみ許可）
     *
     * @param User $user
     * @param Review $review
     * @return bool
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}
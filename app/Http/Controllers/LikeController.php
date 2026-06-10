<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    /**
     * レビューへのいいねを切り替える（登録/解除）
     *
     * @param Review $review
     * @return RedirectResponse
     */
    public function toggle(Review $review): RedirectResponse
    {
        Auth::user()->likedReviews()->toggle($review->id);

        return back()->with('success', 'レビューのいいねを更新しました');
    }

    /**
     * レビューにいいねを登録する
     *
     * @param Request $request
     * @param Review $review
     * @return RedirectResponse
     */
    public function store(Request $request, Review $review): RedirectResponse
    {
        Log::info('Like store called', ['user_id' => Auth::id(), 'review_id' => $review->id]);

        // 既にいいねしている場合にエラーにならないよう syncWithoutDetaching を推奨
        $review->likedByUsers()->syncWithoutDetaching([Auth::id()]);

        return back();
    }
}
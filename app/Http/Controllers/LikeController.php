<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Review $review)
    {
        $user = Auth::user();

        $user->likedReviews()->toggle($review->id);

        $user->load('likedReviews');

        return back()->with('success', 'レビューのいいねを更新しました');
    }
    public function store(Request $request, Review $review)
    {
        \Log::info('User ID: ' . auth()->id());
        \Log::info('Like store called for review: ' . $review->id);

        $review->likedByUsers()->attach(auth()->id());

        return back();
    }
}
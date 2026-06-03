<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     *  レビューに対する「いいね」の登録 / 解除を切り替える（トグル処理）
     */
    public function toggle(Review $review)
    {
        $user = Auth::user();

        // 1. 中間テーブル（review_user）のレコードを切り替える
        $user->likedReviews()->toggle($review->id);

        // 2. メモリ上のユーザーキャッシュを最新状態にする（合言葉：likedReviews）
        $user->load('likedReviews');

        // 直前の画面（書籍詳細画面）にリダイレクト
        return back()->with('success', 'レビューのいいねを更新しました');
    }
}
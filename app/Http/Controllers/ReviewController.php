<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Http\Requests\ReviewCreateRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * レビューの投稿・保存処理
     */
    public function store(ReviewCreateRequest $request, Book $book)
    {
        // 1. バリデーション済みのデータを取得（rating, comment）
        $validated = $request->validated();

        // 2. ログイン中のユーザーIDと、対象の書籍IDを紐付けてレビューを作成
        Review::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // 3. 元の書籍詳細画面に戻り、成功メッセージを表示
        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを投稿しました！');
    }
}
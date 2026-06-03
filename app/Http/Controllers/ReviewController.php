<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Http\Requests\ReviewCreateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        $review->load('book');

        return view('reviews.edit', compact('review'));
    }
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'comment' => 'required|max:1000',
            'rating' => 'required|integer|between:1,5',
        ]);

        $review->update($validated);
        return redirect()->route('books.show', $review->book_id)->with('success', 'レビューを更新しました。');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $bookId = $review->book_id;
        $review->delete();

        return redirect()->route('books.show', $bookId)->with('success', 'レビューを削除しました。');
    }
}
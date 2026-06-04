<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * レビューの投稿・保存処理
     */
    public function store(ReviewRequest $request, Book $book)
    {
        // ログインユーザーと紐付けて作成
        $book->reviews()->create(array_merge($request->validated(), [
            'user_id' => Auth::id(),
        ]));

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
    public function update(ReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        // バリデーション済みデータで更新
        $review->update($request->validated());

        return redirect()
            ->route('books.show', $review->book_id)
            ->with('success', 'レビューを更新しました。');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $bookId = $review->book_id;
        $review->delete();

        return redirect()->route('books.show', $bookId)->with('success', 'レビューを削除しました。');
    }
}
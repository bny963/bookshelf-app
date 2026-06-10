<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\ReviewUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * レビューを投稿・保存する
     *
     * @param ReviewRequest $request
     * @param Book $book
     * @return RedirectResponse
     */
    public function store(ReviewRequest $request, Book $book): RedirectResponse
    {
        $book->reviews()->create(array_merge($request->validated(), [
            'user_id' => Auth::id(),
        ]));

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを投稿しました！');
    }

    /**
     * レビュー編集画面を表示する
     *
     * @param Review $review
     * @return View
     */
    public function edit(Review $review): View
    {
        $this->authorize('update', $review);
        $review->load('book');

        return view('reviews.edit', compact('review'));
    }

    /**
     * レビューを更新する
     *
     * @param ReviewUpdateRequest $request
     * @param Review $review
     * @return RedirectResponse
     */
    public function update(ReviewUpdateRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $review->update($request->validated());

        return redirect()
            ->route('books.show', $review->book_id)
            ->with('success', 'レビューを更新しました。');
    }

    /**
     * レビューを削除する
     *
     * @param Review $review
     * @return RedirectResponse
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $bookId = $review->book_id;
        $review->delete();

        return redirect()->route('books.show', $bookId)->with('success', 'レビューを削除しました。');
    }
}
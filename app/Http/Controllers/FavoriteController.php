<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * お気に入りの状態を切り替える（登録/解除）
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function toggle(Book $book): RedirectResponse
    {
        $user = Auth::user();
        $user->favoriteBooks()->toggle($book->id);

        return back()->with('success', 'お気に入りを更新しました');
    }

    /**
     * お気に入り書籍一覧を表示する
     *
     * @return View
     */
    public function index(): View
    {
        $books = Auth::user()->favoriteBooks()->paginate(10);

        return view('favorites.index', compact('books'));
    }

    /**
     * お気に入りに登録する
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function store(Book $book): RedirectResponse
    {
        Auth::user()->favoriteBooks()->syncWithoutDetaching([$book->id]);

        return redirect()->route('books.index');
    }

    /**
     * お気に入りを解除する
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function destroy(Book $book): RedirectResponse
    {
        Auth::user()->favoriteBooks()->detach($book->id);

        return redirect()->route('books.index');
    }
}
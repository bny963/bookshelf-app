<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    public function toggle(Book $book)
    {
        $user = Auth::user();

       
        $user->favoriteBooks()->toggle($book->id);

        
        $user->load('favoriteBooks');

        
        return back()->with('success', 'お気に入りを更新しました');
    }
    public function index()
    {
        $books = Auth::user()->favoriteBooks()->paginate(10);

        return view('favorites.index', compact('books'));
    }
    public function store(Book $book)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // お気に入り登録（まだ登録されていない場合のみ実行）
        if (!$user->favoriteBooks()->where('book_id', $book->id)->exists()) {
            $user->favoriteBooks()->attach($book->id);
        }

        return redirect()->route('books.index');
    }

    public function destroy(Book $book)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // お気に入り解除
        $user->favoriteBooks()->detach($book->id);

        return redirect()->route('books.index');
    }
}
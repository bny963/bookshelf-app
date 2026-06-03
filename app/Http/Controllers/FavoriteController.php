<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
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
}
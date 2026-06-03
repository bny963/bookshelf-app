<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        // withCount('books') を使うと、$genre->books_count が自動的に計算されます
        $genres = Genre::withCount('books')->get();
        return view('genres.index', compact('genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:genres|max:255',
        ]);

        Genre::create($validated);

        return redirect()->route('genres.index')->with('success', 'ジャンルを登録しました。');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('genres.index')->with('success', 'ジャンルを削除しました。');
    }
    public function create()
    {
        return view('genres.create');
    }
    public function show(Genre $genre)
    {
        $books = $genre->books()->with('genres')->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }
    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }
    public function update(Request $request, Genre $genre)
    {
        // 入力チェック（自分自身以外の重複は許可する設定）
        $validated = $request->validate([
            'name' => 'required|max:255|unique:genres,name,' . $genre->id,
        ]);

        $genre->update($validated);

        return redirect()->route('genres.index')->with('success', 'ジャンルを更新しました。');
    }
}
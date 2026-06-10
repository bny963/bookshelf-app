<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\GenreStoreRequest;
use App\Http\Requests\GenreUpdateRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GenreController extends Controller
{
    /**
     * ジャンル一覧を表示する
     *
     * @return View
     */
    public function index(): View
    {
        $genres = Genre::withCount('books')->get();
        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル登録画面を表示する
     *
     * @return View
     */
    public function create(): View
    {
        return view('genres.create');
    }

    /**
     * 新しいジャンルを保存する
     *
     * @param GenreStoreRequest $request
     * @return RedirectResponse
     */
    public function store(GenreStoreRequest $request): RedirectResponse
    {
        Genre::create($request->validated());
        return redirect()->route('genres.index')->with('success', 'ジャンルを登録しました。');
    }

    /**
     * 特定のジャンル詳細と書籍一覧を表示する
     *
     * @param Genre $genre
     * @return View
     */
    public function show(Genre $genre): View
    {
        $books = $genre->books()->with('genres')->paginate(10);
        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンル編集画面を表示する
     *
     * @param Genre $genre
     * @return View
     */
    public function edit(Genre $genre): View
    {
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンル情報を更新する
     *
     * @param GenreUpdateRequest $request
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function update(GenreUpdateRequest $request, Genre $genre): RedirectResponse
    {
        $genre->update($request->validated());
        return redirect()->route('genres.index')->with('success', 'ジャンルを更新しました。');
    }

    /**
     * ジャンルを削除する
     *
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();
        return redirect()->route('genres.index')->with('success', 'ジャンルを削除しました。');
    }
}
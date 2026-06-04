<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * 書籍一覧を取得
     * GET /api/v1/books
     */
    public function index(Request $request)
    {
        $books = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            // ジャンル検索がある場合は絞り込み
            ->when($request->filled('genre_id'), function ($query) use ($request) {
                $query->whereHas('genres', function ($q) use ($request) {
                    $q->where('genres.id', $request->genre_id);
                });
            })
            ->paginate(10);

        return BookResource::collection($books);
    }

    /**
     * 書籍詳細を取得
     * GET /api/v1/books/{book}
     */
    public function show($id)
    {
        // findOrFail を使用することで、存在しない場合は自動的に404エラーを返します
        $book = Book::with(['genres', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        return new BookResource($book);
    }
    /**
     * 書籍を新規登録
     * POST /api/v1/books
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|size:13',
            'published_date' => 'required|date',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $book = new Book($request->except('genres'));
        $book->user_id = auth()->id() ?? 1; 
        $book->save();

        $book->genres()->sync($request->genres);

        return (new BookResource($book->load('genres')))->response()->setStatusCode(201);
    }

    /**
     * 書籍を更新
     * PUT /api/v1/books/{book}
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|size:13',
            'published_date' => 'required|date',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $book->update($request->except('genres'));
        $book->genres()->sync($request->genres);

        return new BookResource($book->load('genres'));
    }

    /**
     * 書籍を削除
     * DELETE /api/v1/books/{book}
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // 中間テーブルのレコードを削除
        $book->genres()->detach();
        $book->delete();

        return response()->json(['message' => '書籍を削除しました。'], 200);
    }
}
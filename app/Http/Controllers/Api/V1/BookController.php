<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Requests\Api\V1\BookIndexRequest;
use App\Http\Requests\Api\V1\BookRequest;

class BookController extends Controller
{
    public function index(BookIndexRequest $request)
    {
        $books = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->genre_id, fn($q) => $q->whereHas('genres', fn($g) => $g->where('id', $request->genre_id)))
            ->paginate($request->per_page ?? 10);

        return BookResource::collection($books);
    }

    public function show($id)
    {
        $book = Book::with(['genres', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        return new BookResource($book);
    }

    public function store(BookRequest $request)
    {
        $book = Book::create($request->validated());
        $book->user_id = auth()->id() ?? 1;
        $book->save();
        $book->genres()->sync($request->genres);

        return (new BookResource($book->load('genres')))->response()->setStatusCode(201);
    }

    public function update(BookRequest $request, $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->validated());
        $book->genres()->sync($request->genres);

        return new BookResource($book->load('genres'));
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->genres()->detach();
        $book->delete();

        return response()->json(['message' => '書籍を削除しました。'], 200);
    }
}
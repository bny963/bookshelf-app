<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Requests\Api\V1\BookIndexRequest;
use App\Http\Requests\Api\V1\BookRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\Api\V1\BookUpdateRequest;

class BookController extends Controller
{
    use AuthorizesRequests;
    public function index(BookIndexRequest $request)
    {
        $books = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->genre_id, fn($q) => $q->whereHas('genres', fn($g) => $g->where('genres.id', $request->genre_id)))
            ->when($request->keyword, fn($q) => $q->where('title', 'like', "%{$request->keyword}%"))
            ->when($request->sort === 'latest', fn($q) => $q->latest())
            ->when($request->sort === 'oldest', fn($q) => $q->oldest())
            ->paginate($request->per_page ?? 10);

        $books->appends($request->query());
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
        $book = Book::create($request->validated() + ['user_id' => auth()->id()]);
        $book->genres()->sync($request->genres);

        return (new BookResource($book->load('genres')))->response()->setStatusCode(201);
    }

    public function update(BookUpdateRequest $request, $id)
    {
        $book = Book::findOrFail($id);

        // バリデーション（$request->validated()）の前に認可を行う！
        $this->authorize('update', $book);

        $book->update($request->validated());
        $book->genres()->sync($request->genres);

        return new BookResource($book->load('genres'));
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(['message' => '書籍を削除しました。'], 200);
    }
    public function showIsbn($isbn)
    {
        if (!preg_match('/^[0-9]{13}$/', $isbn)) {
            return response()->json(['message' => 'Invalid ISBN format'], 422);
        }

        $cleanIsbn = str_replace('-', '', $isbn);
        $apiKey = config('services.google_books.api_key');

        $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$cleanIsbn}&key={$apiKey}";
        $response = Http::get($url);

        if ($response->successful() && isset($response['items'][0])) {
            $info = $response['items'][0]['volumeInfo'];

            $imageUrl = $info['imageLinks']['thumbnail'] ?? null;

            return response()->json([
                'title' => $info['title'] ?? '',
                'author' => isset($info['authors']) ? implode(', ', $info['authors']) : '',
                'description' => $info['description'] ?? '',
                'published_date' => $info['publishedDate'] ?? null,
                'image_url' => $imageUrl,
            ]);
        }

        return response()->json(['error' => '指定された ISBN の書籍は見つかりませんでした。'], 404);
    }
}
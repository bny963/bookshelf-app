<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Requests\Api\V1\BookIndexRequest;
use App\Http\Requests\Api\V1\BookRequest;
use App\Http\Requests\Api\V1\BookUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    use AuthorizesRequests;

    /**
     * 書籍一覧を取得する
     *
     * @param BookIndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(BookIndexRequest $request): AnonymousResourceCollection
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

    /**
     * 特定の書籍詳細を取得する
     *
     * @param int $id
     * @return BookResource
     */
    public function show(int $id): BookResource
    {
        $book = Book::with(['genres', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        return new BookResource($book);
    }

    /**
     * 書籍を新規登録する
     *
     * @param BookRequest $request
     * @return JsonResponse
     */
    public function store(BookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated() + ['user_id' => auth()->id()]);
        $book->genres()->sync($request->genres);

        return (new BookResource($book->load('genres')))->response()->setStatusCode(201);
    }

    /**
     * 書籍情報を更新する
     *
     * @param BookUpdateRequest $request
     * @param int $id
     * @return BookResource
     */
    public function update(BookUpdateRequest $request, int $id): BookResource
    {
        $book = Book::findOrFail($id);
        $this->authorize('update', $book);

        $book->update($request->validated());
        $book->genres()->sync($request->genres);

        return new BookResource($book->load('genres'));
    }

    /**
     * 書籍を削除する
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $book = Book::findOrFail($id);
        $this->authorize('delete', $book);

        $book->delete();

        return response()->json(['message' => '書籍を削除しました。'], 200);
    }

    /**
     * ISBNから外部API経由で書籍情報を取得する
     *
     * @param string $isbn
     * @return JsonResponse
     */
    public function showIsbn(string $isbn): JsonResponse
    {
        if (!preg_match('/^[0-9]{13}$/', $isbn)) {
            return response()->json(['message' => 'Invalid ISBN format'], 422);
        }

        $apiKey = config('services.google_books.api_key');
        $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}&key={$apiKey}";
        $response = Http::get($url);

        if ($response->successful() && isset($response['items'][0])) {
            $info = $response['items'][0]['volumeInfo'];

            return response()->json([
                'title' => $info['title'] ?? '',
                'author' => isset($info['authors']) ? implode(', ', $info['authors']) : '',
                'description' => $info['description'] ?? '',
                'published_date' => $info['publishedDate'] ?? null,
                'image_url' => $info['imageLinks']['thumbnail'] ?? null,
            ]);
        }

        return response()->json(['error' => '指定された ISBN の書籍は見つかりませんでした。'], 404);
    }
}
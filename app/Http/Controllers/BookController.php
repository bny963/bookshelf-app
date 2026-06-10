<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use App\Http\Requests\BookUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * 書籍一覧・検索画面の表示
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $genres = Genre::all();
        $query = Book::query();

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('author', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->input('genre'));
            });
        }

        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'oldest' => $query->orderBy('published_date', 'asc'),
            'title' => $query->orderBy('title', 'asc'),
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            default => $query->orderBy('published_date', 'desc'),
        };

        $books = $query->with(['genres', 'reviews'])->paginate(10)->withQueryString();

        return view('books.index', compact('books', 'genres'));
    }

    /**
     * 書籍登録画面の表示
     *
     * @return View
     */
    public function create(): View
    {
        $book = new Book();
        $book->setRelation('genres', collect());
        $genres = Genre::all();

        return view('books.create', compact('book', 'genres'));
    }

    /**
     * 書籍を保存する
     *
     * @param BookRequest $request
     * @return RedirectResponse
     */
    public function store(BookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $book = Book::create($data);
        $book->genres()->sync($data['genres'] ?? []);

        return redirect()->route('books.index')->with('success', '登録しました');
    }

    /**
     * 書籍詳細画面の表示
     *
     * @param Book $book
     * @return View
     */
    public function show(Book $book): View
    {
        $book->load(['reviews.user', 'reviews.likedByUsers', 'genres']);

        if (auth()->check()) {
            auth()->user()->refresh()->load(['favoriteBooks', 'likedReviews']);
        }

        return view('books.show', compact('book'));
    }

    /**
     * 書籍ランキング画面の表示
     *
     * @return View
     */
    public function ranking(): View
    {
        $rankedBooks = Book::withAvg('reviews', 'rating')
            ->has('reviews')
            ->orderBy('reviews_avg_rating', 'desc')
            ->take(10)
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }

    /**
     * 書籍編集画面の表示
     *
     * @param Book $book
     * @return View
     */
    public function edit(Book $book): View
    {
        $this->authorize('update', $book);
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍情報を更新する
     *
     * @param BookUpdateRequest $request
     * @param Book $book
     * @return RedirectResponse
     */
    public function update(BookUpdateRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);
        $data = $request->validated();

        $book->update($data);

        if (isset($data['genres'])) {
            $book->genres()->sync($data['genres']);
        }

        return redirect()->route('books.show', $book)->with('success', '書籍情報を更新しました。');
    }

    /**
     * 書籍を削除する
     *
     * @param Book $book
     * @return RedirectResponse
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);
        $book->delete();

        return redirect()->route('books.index')->with('success', '書籍を削除しました。');
    }

    /**
     * ISBN検索（外部API）
     *
     * @param string $isbn
     * @return JsonResponse
     */
    public function isbnSearch(string $isbn): JsonResponse
    {
        $cleanIsbn = str_replace('-', '', $isbn);
        $apiKey = config('services.google_books.api_key');
        $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$cleanIsbn}&key={$apiKey}";

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
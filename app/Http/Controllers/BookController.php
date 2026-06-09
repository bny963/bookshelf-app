<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use App\Http\Requests\BookUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;



class BookController extends Controller
{
    /**
     * 書籍一覧・検索画面の表示
     */
    public function index(Request $request)
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
                $q->where('genres.id', $request->input('genre')); // 受け取った 'genre' を使用
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

    public function create()
    {
        $book = new Book();

        $book->setRelation('genres', collect());

        // ジャンル一覧を取得
        $genres = Genre::all();

        return view('books.create', compact('book', 'genres'));
    }

    public function store(BookRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        $book = Book::create($data); 

        $book->genres()->sync($data['genres']);

        return redirect()->route('books.index')->with('success', '登録しました');
    }

    public function show(Book $book)
    {
        $book->load(['reviews.user', 'reviews.likedByUsers', 'genres']);

        if (auth()->check()) {
            auth()->user()->refresh()->load(['favoriteBooks', 'likedReviews']);
        }

        return view('books.show', compact('book'));
    }
       public function ranking()
    {
        $rankedBooks = Book::withAvg('reviews', 'rating') 
            ->has('reviews')
            ->orderBy('reviews_avg_rating', 'desc') 
            ->take(10)
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }
    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }
    public function update(BookUpdateRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        // バリデーション済みデータを取得
        $data = $request->validated();

        // 1. 書籍データのみを抽出 (fillable対象のみにする)
        $bookData = [
            'title' => $data['title'],
            'author' => $data['author'],
            'isbn' => $data['isbn'],
            'published_date' => $data['published_date'],
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null,
        ];

        // 2. 書籍情報の更新
        $book->update($bookData);

        // 3. ジャンルの同期 (ここだけリレーションを扱う)
        if (isset($data['genres'])) {
            $book->genres()->sync($data['genres']);
        }

        return redirect()->route('books.show', $book)->with('success', '書籍情報を更新しました。');
    }
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->delete();
        return redirect()->route('books.index')->with('success', '書籍を削除しました。');
    }
    public function isbnSearch($isbn)
    {
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
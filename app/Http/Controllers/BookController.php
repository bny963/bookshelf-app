<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Http\Requests\BookSearchRequest;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧・検索画面の表示
     */
    public function index(Request $request) 
    {
        // 1. ジャンル一覧を取得（絞り込み用）
        $genres = Genre::all();

        // 2. 書籍取得クエリの土台を作成
        $query = Book::query();

        // 3. ジャンル検索（完全一致）
        if ($request->filled('genre_id')) {
            $query->where('genre_id', $request->input('genre_id'));
        }

        // 4. クエリを実行して結果を取得（1ページ10件）
        $books = $query->with('genre')->paginate(10);

        // 検索条件（ジャンルID）をページネーションのリンクに引き継ぐ
        $books->appends($request->all());

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
        // すでにバリデーション＆ハイフン除去が済んだデータを受け取る
        $validated = $request->validated();

        $genreId = is_array($validated['genres']) ? ($validated['genres'][0] ?? null) : $validated['genres'];

        // データベースへ保存
        $book = Book::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'],
            'image_url' => $validated['image_url'],
        ]);

        return redirect()->route('books.index')->with('success', '新しい書籍を登録しました！');
    }

    public function show(Book $book)
    {
        $book->load(['reviews.user', 'reviews.likedByUsers', 'genre']);

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
    
}
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Http\Requests\BookSearchRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * 書籍一覧・検索画面の表示
     */
    public function index(BookSearchRequest $request)
    {
        // 1. 検索用のプルダウンに使うために、全ジャンルを取得
        $genres = Genre::all();

        // 2. 書籍取得クエリの土台を作成
        $query = Book::query();

        // 3. キーワード検索（タイトルまたは著者名に部分一致）
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
        }

        // 4. ジャンル検索（完全一致）
        if ($request->filled('genre_id')) {
            $query->where('genre_id', $request->input('genre_id'));
        }

        // 5. ページネーションを適用して取得（1ページ12件など、Bladeのレイアウトに合わせて調整してください）
        $books = $query->latest()->paginate(12)->withQueryString();

        // 6. 既存の一覧画面Bladeにデータを渡して表示
        return view('books.index', compact('books', 'genres'));
    }
    /**
     * 【仮追加】書籍登録画面の表示（後ほど実装します）
     */
    public function create()
    {
        return '書籍登録画面（開発中）';
    }

    /**
     * 【仮追加】書籍詳細画面の表示（後ほど実装します）
     */
    public function show($id)
    {
        return '書籍詳細画面（開発中）：ID ' . $id;
    }
}
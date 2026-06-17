<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * マイ読書レポート画面を表示する
     *
     * @return View
     */
    public function index(): View
    {
        $userId = Auth::id();

        $stats['summary'] = [
            'total_reviews' => Review::where('user_id', $userId)->count(),
            'books_read'    => Review::where('user_id', $userId)->distinct('book_id')->count(),
            'average_rating' => Review::where('user_id', $userId)->avg('rating') ?? 0,
        ];

        $distribution = Review::where('user_id', $userId)
            ->whereBetween('rating', [1, 5])
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $stats['rating_distribution'] = collect(range(1, 5))->map(fn($r) => $distribution[$r] ?? 0);

        $stats['top_rated_books'] = Book::whereHas('reviews', fn($q) => $q->where('user_id', $userId))
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get()
            ->map(fn($book) => [
                'id'     => $book->id,
                'title'  => $book->title,
                'author' => $book->author,
                'rating' => (int) round($book->reviews_avg_rating),
            ]);

        $stats['genre_ratings'] = Genre::query()
            ->select('genres.id', 'genres.name')
            ->selectRaw('AVG(reviews.rating) as average_rating')
            ->selectRaw('COUNT(DISTINCT books.id) as count')
            ->join('book_genre', 'genres.id', '=', 'book_genre.genre_id')
            ->join('books', 'book_genre.book_id', '=', 'books.id')
            ->join('reviews', 'books.id', '=', 'reviews.book_id')
            ->where('reviews.user_id', $userId)
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('average_rating')
            ->take(5)
            ->get();

        return view('reports.index', compact('stats'));
    }
}

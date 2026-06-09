<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userReviews = Review::where('user_id', $userId);

        // 1. 基本統計の集計
        $stats = [
            'summary' => [
                'total_reviews' => $userReviews->count(),
                'books_read' => $userReviews->distinct('book_id')->count(),
                'average_rating' => $userReviews->avg('rating'),
            ],
            // 2. 評価分布の集計
            'rating_distribution' => $userReviews->select('rating', \DB::raw('count(*) as count'))
                ->groupBy('rating')
                ->pluck('count', 'rating'), // 配列として渡す
        ];

        // 3. 高評価書籍TOP5 (top_rated_books)
        $stats['top_rated_books'] = \App\Models\Book::whereHas('reviews', fn($q) => $q->where('user_id', $userId))
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get()
            ->map(fn($book) => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'rating' => round($book->reviews_avg_rating),
            ]);

        // 4. ジャンル別評価傾向TOP5 (genre_ratings)
        $stats['genre_ratings'] = \App\Models\Genre::query()
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
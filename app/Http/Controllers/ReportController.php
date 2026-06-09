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

        $totalReviews = Review::where('user_id', $userId)->count();
        $booksRead = Review::where('user_id', $userId)->distinct('book_id')->count();
        $avgRating = Review::where('user_id', $userId)->avg('rating');

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $booksRead,
                'average_rating' => $avgRating,
            ],
        ];
        $distribution = Review::where('user_id', $userId)
            ->whereBetween('rating', [1, 5])
            ->select('rating', \DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // 2. 1〜5をキーとして強制的に作成（データがない場合は 0 を入れる）
        $fullDistribution = [];
        for ($i = 0; $i <= 4; $i++) {
            $rating = $i + 1; // 1, 2, 3, 4, 5
            $fullDistribution[$i] = $distribution[$rating] ?? 0;
        }

        // 3. これで $fullDistribution は [0=>★1の数, 1=>★2の数, ..., 4=>★5の数] になる
// 現在はこれで 1 から順に並んでいるはずなので、そのまま渡します
        $stats['rating_distribution'] = collect($fullDistribution);

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
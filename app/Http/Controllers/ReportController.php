<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Review;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
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
            'total_reviews'  => Review::where('user_id', $userId)->count(),
            'books_read'     => Review::where('user_id', $userId)->distinct('book_id')->count(),
            'average_rating' => Review::where('user_id', $userId)->avg('rating') ?? 0,
        ];

        $distribution = Review::where('user_id', $userId)
            ->whereBetween('rating', [1, 5])
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $stats['rating_distribution'] = collect(range(1, 5))->map(fn($rating) => $distribution[$rating] ?? 0);

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

        $stats['genre_ratings'] = Genre::whereHas('books', fn($q) =>
            $q->whereHas('reviews', fn($r) => $r->where('user_id', $userId))
        )
        ->with(['books' => fn($q) => $q
            ->whereHas('reviews', fn($r) => $r->where('user_id', $userId))
            ->with(['reviews' => fn($r) => $r->where('user_id', $userId)])
        ])
        ->get()
        ->map(fn($genre) => [
            'id'             => $genre->id,
            'name'           => $genre->name,
            'count'          => $genre->books->count(),
            'average_rating' => $genre->books->flatMap->reviews->avg('rating') ?? 0,
        ])
        ->sortByDesc('average_rating')
        ->take(5)
        ->values();

        return view('reports.index', compact('stats'));
    }
}

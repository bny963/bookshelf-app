<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * ユーザーの読書分析レポートを取得する
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 基礎統計データの取得
        $stats = [
            'summary' => [
                'total_reviews' => Review::where('user_id', $userId)->count(),
                'books_read' => Review::where('user_id', $userId)->distinct('book_id')->count(),
                'average_rating' => Review::where('user_id', $userId)->avg('rating'),
            ],
        ];

        // 評価分布の集計
        $distribution = Review::where('user_id', $userId)
            ->whereBetween('rating', [1, 5])
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $stats['rating_distribution'] = collect(range(1, 5))->map(fn($rating) => $distribution[$rating] ?? 0);

        // 高評価書籍TOP5
        $stats['top_rated_books'] = Book::whereHas('reviews', fn($q) => $q->where('user_id', $userId))
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get()
            ->map(fn($book) => [
                'id'     => $book->id,
                'title'  => $book->title,
                'author' => $book->author,
                'rating' => round($book->reviews_avg_rating),
            ]);

        // ジャンル別評価統計
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

        return response()->json($stats);
    }
}
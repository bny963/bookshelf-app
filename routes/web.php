<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReadingPlanController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;


/**
 * 認証必須ルート
 */
Route::middleware('auth')->group(function () {
    // 書籍管理
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::resource('books', BookController::class)->except(['index', 'show', 'create']);

    // レビュー管理
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // お気に入り・いいね管理
    Route::post('/books/{book}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/favorites/{book}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{book}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::post('/reviews/{review}/like', [LikeController::class, 'toggle'])->name('reviews.like');
    Route::post('/likes/{review}', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/likes/{review}', [LikeController::class, 'destroy'])->name('likes.destroy');

    // その他機能
    Route::resource('genres', GenreController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/notifications', fn() => view('notifications.index'))->name('notifications.index');

    // 読書計画管理
    Route::post('/reading-plans/{reading_plan}/complete', [ReadingPlanController::class, 'complete'])->name('reading-plans.complete');
    Route::resource('reading-plans', ReadingPlanController::class)->except(['show']);
});

/**
 * 認証不要ルート
 */
Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);
Route::get('/ranking', [BookController::class, 'ranking'])->name('ranking.index');
Route::get('/books/isbn/{isbn}', [BookController::class, 'isbnSearch'])->name('books.isbnSearch');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\Api\ReportController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);

Route::get('/ranking', [BookController::class, 'ranking'])->name('ranking.index');

Route::middleware('auth')->group(function () {
    // 書籍のリソースルート
    Route::resource('books', BookController::class)->except(['index', 'show']);

    // レビュー関係
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // その他
    Route::post('/books/{book}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/reviews/{review}/like', [LikeController::class, 'toggle'])->name('reviews.like');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::resource('genres', GenreController::class);
    Route::get('/reports', [App\Http\Controllers\Api\ReportController::class, 'index'])->name('reports.index');
    Route::get('/notifications', function () {
        return view('notifications.index'); })->name('notifications.index');
    Route::get('/reading-plans', function () {
        return view('reading-plans.index'); })->name('reading-plans.index');
});

Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

Route::post('/favorites/{book}', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/favorites/{book}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
Route::post('/likes/{review}', [LikeController::class, 'store'])->name('likes.store');
Route::delete('/likes/{review}', [LikeController::class, 'destroy'])->name('likes.destroy');
Route::get('/books/isbn/{isbn}', [App\Http\Controllers\BookController::class, 'isbnSearch'])->name('books.isbnSearch');

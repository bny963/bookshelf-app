<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LikeController;

// 1. まず一覧画面を定義
Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);

// ナビゲーションバーのリンクエラーを防ぐための仮定義（共通）
Route::get('/ranking', function () {
    return 'ランキング画面（開発中）';
})->name('ranking.index');
Route::get('/favorites', function () {
    return 'お気に入り画面（開発中）';
})->name('favorites.index');
Route::get('/genres', function () {
    return 'ジャンル管理画面（開発中）';
})->name('genres.index');

// 2. ログイン必須のルートグループ（固定URLを上に配置）
Route::middleware('auth')->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');

    // レビュー投稿の保存処理
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::post('/books/{book}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/reviews/{review}/like', [LikeController::class, 'toggle'])->name('reviews.like');
});

// 3. 最後に変数を含むルート（詳細画面）
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
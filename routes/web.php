<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// ⚠️ ナビゲーションバーのリンクエラーを防ぐための仮定義（共通）
Route::get('/ranking', function () {
    return 'ランキング画面（開発中）'; })->name('ranking.index');
Route::get('/favorites', function () {
    return 'お気に入り画面（開発中）'; })->name('favorites.index');
Route::get('/genres', function () {
    return 'ジャンル管理画面（開発中）'; })->name('genres.index');

Route::middleware('auth')->group(function () {

    // 書籍登録画面の表示（仮）
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');

    // レビュー投稿の保存処理
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // お気に入り登録・解除の処理（仮）
    Route::post('/books/{book}/favorite', function () {
        return 'お気に入り処理（開発中）';
    })->name('favorites.toggle');

    Route::post('/reviews/{review}/like', function () {
        return 'いいね処理（開発中）';
    })->name('reviews.like');

});
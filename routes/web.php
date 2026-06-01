<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

// 書籍関連のルート
Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');


// ランキング画面用
Route::get('/ranking', function () {
    return 'ランキング画面（開発中）';
})->name('ranking.index');

// お気に入り画面用
Route::get('/favorites', function () {
    return 'お気に入り画面（開発中）';
})->name('favorites.index');

// ジャンル管理画面用（要件にジャンル登録・編集があるため）
Route::get('/genres', function () {
    return 'ジャンル管理画面（開発中）';
})->name('genres.index');
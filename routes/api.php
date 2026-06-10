<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;

/**
 * 認証関連ルート
 */
Route::post('/login', [AuthController::class, 'login']);

/**
 * API V1 ルートグループ
 */
Route::prefix('v1')->group(function () {
    // 認証不要ルート
    Route::get('/books', [BookController::class, 'index'])->name('api.v1.books.index');
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::get('/books/isbn/{isbn}', [BookController::class, 'showIsbn']);

    // 認証必須ルート
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::patch('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);

        Route::get('/report', [ReportController::class, 'index']);
    });
});
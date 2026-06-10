<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('v1')->group(function () {
    Route::get('/books', [BookController::class, 'index'])->name('api.v1.books.index');
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::get('/books/isbn/{isbn}', [BookController::class, 'showIsbn']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books', [BookController::class, 'store']);
        Route::patch('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
        Route::get('/report', [ReportController::class, 'index']);
        // Route::get('/books', [BookApiController::class, 'index']);
        // Route::patch('/books/{book}', [BookApiController::class, 'update']);
    });
});
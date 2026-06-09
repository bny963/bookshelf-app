<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;

class BookApiController extends Controller
{
    public function index()
    {
        // 認証済みのユーザーのみがこの処理に到達できる
        return response()->json([
            'data' => Book::with(['genres', 'reviews'])->paginate(10)
        ]);
    }
    public function update(Request $request, Book $book)
    {
        // 1. 認可チェック（所有者か？違えば 403 を返して終了）
        $this->authorize('update', $book);

        // 2. 正常な更新処理
        $book->update($request->all());
        return response()->json(['message' => '更新しました']);
    }
}
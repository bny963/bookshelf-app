<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $userId = auth()->id();
        $bookId = $this->route('book')->id; // ルートから書籍IDを取得

        return [
            'comment' => 'required|max:1000',
            'rating' => 'required|integer|between:1,5',
            // 複合ユニーク制約のルール適用
            'book_id' => [
                Rule::unique('reviews')->where(function ($query) use ($userId, $bookId) {
                    return $query->where('user_id', $userId)->where('book_id', $bookId);
                }),
            ],
        ];
    }
}
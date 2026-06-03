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
        return [
            'rating' => 'required|integer|min:1|max:5', // 評価（星1〜5の数値）
            'comment' => 'nullable|string|max:1000',     // コメント（空っぽでもOK、最大1000文字）
        ];
    }
}
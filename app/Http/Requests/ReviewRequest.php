<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5', // 評価値の範囲
            'comment' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.min' => '評価は1以上で入力してください。',
            'rating.max' => '評価は5以下で入力してください。',
        ];
    }
}
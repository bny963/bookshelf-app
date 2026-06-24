<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReadingPlanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id'     => 'required|exists:books,id',
            'target_date' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required'          => '書籍は必須です。',
            'book_id.exists'            => '選択した書籍が見つかりません。',
            'target_date.required'      => '期日は必須です。',
            'target_date.date'          => '期日は正しい日付形式で入力してください。',
            'target_date.after_or_equal' => '期日は今日以降の日付を入力してください。',
        ];
    }
}

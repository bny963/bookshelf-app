<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function rules(): array
    {
        // 更新時は現在の書籍IDを取得
        $bookId = $this->route('book'); 

        return [
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            // 更新時は自分自身を除外してISBNの一意性をチェック
            'isbn' => 'nullable|digits:13|unique:books,isbn,' . $bookId,
            'published_date' => 'nullable|date',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ];
    }
        public function messages(): array
    {
        return [
            'user_id.exists' => '指定されたユーザーIDは存在しません。',
        ];
    }
}
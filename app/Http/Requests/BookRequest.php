<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * 認証のチェック（今回は一律 true で許可します）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     *  バリデーション前の前処理（ここでハイフンを除去）
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('isbn')) {
            // 文字列からハイフン「-」をすべて削除し、半角に統一
            $cleanIsbn = str_replace('-', '', $this->isbn);

            // 変換した値をリクエストデータに再セットする
            $this->merge([
                'isbn' => $cleanIsbn,
            ]);
        }
    }


    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|digits:13',
            'published_date' => 'nullable|date',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ];
    }


    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'author.required' => '著者名は必須です。',
            'isbn.digits' => 'ISBNはハイフンを除いた13桁の数字で入力してください。',
            'genres.required' => 'ジャンルは必須です。',
        ];
    }
}
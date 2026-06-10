<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 書籍一覧取得時のバリデーションクラス
 */
class BookIndexRequest extends FormRequest
{
    /**
     * リクエストがこのリクエストを行う権限を持っているか判定
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルールを取得
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|max:100',
            'genre_id' => 'nullable|exists:genres,id',
            'sort' => 'nullable|in:latest,oldest',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ];
    }
}
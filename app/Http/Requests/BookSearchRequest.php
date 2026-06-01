<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookSearchRequest extends FormRequest
{
    /**
     * リクエストの実行を認可するか（今回は全員に許可するため true にします）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルールの定義
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'genre_id' => ['nullable', 'integer', 'exists:genres,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ];
    }

    /**
     * 要件：messages() メソッドを実装し、日本語のバリデーションメッセージを定義すること
     */
    public function messages(): array
    {
        return [
            'keyword.max' => '検索キーワードは255文字以内で入力してください。',
            'genre_id.exists' => '選択されたジャンルは存在しません。',
            'page.integer' => 'ページ番号は整数で指定してください。',
            'per_page.between' => '1ページあたりの件数は1〜100件の間で指定してください。',
        ];
    }
}
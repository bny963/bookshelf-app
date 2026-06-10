<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 書籍登録・更新時のバリデーションクラス
 */
class BookRequest extends FormRequest
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
     * バリデーション前の前処理
     * ISBNのハイフンを除去し、データ形式を統一する
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('isbn')) {
            // 文字列からハイフン「-」をすべて削除
            $cleanIsbn = str_replace('-', '', $this->isbn);

            // 変換した値をリクエストデータに再セットする
            $this->merge([
                'isbn' => $cleanIsbn,
            ]);
        }
    }

    /**
     * バリデーションルールを取得
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // ルートから現在の書籍IDを取得（更新時のユニークチェック用）
        $bookId = $this->route('book');

        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            // unique:テーブル名,カラム名,除外ID
            'isbn' => 'nullable|string|digits:13|unique:books,isbn,' . $bookId,
            'published_date' => 'nullable|date',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ];
    }

    /**
     * バリデーションエラーメッセージのカスタマイズ
     *
     * @return array<string, string>
     */
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
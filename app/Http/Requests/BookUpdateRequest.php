<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 書籍更新時のバリデーションクラス
 */
class BookUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // ルートパラメータから対象の書籍を取得（モデルまたはID）
        $book = $this->route('book');
        $bookId = $book instanceof \App\Models\Book ? $book->id : $book;

        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            // 更新時は自分自身を除外してISBNの一意性をチェック
            'isbn' => 'nullable|digits:13|unique:books,isbn,' . $bookId,
            'published_date' => 'nullable|date',
            'description' => 'nullable|string',
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:genres,id',
            'image_url' => 'nullable|url',
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
            'isbn.unique' => 'このISBNは他の書籍で使用されています。',
            'genres.required' => '少なくとも1つ以上のジャンルを選択してください。',
            'genres.min' => '少なくとも1つ以上のジャンルを選択してください。',
        ];
    }
}
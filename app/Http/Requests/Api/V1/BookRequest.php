<?php

namespace App\Http\Requests\Api\V1;

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
     * バリデーションルールを取得
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // 更新時は現在の書籍IDを取得
        $bookId = $this->route('book');

        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            // 更新時は自分自身を除外してISBNの一意性をチェック
            'isbn' => 'nullable|digits:13|unique:books,isbn,' . $bookId,
            'published_date' => 'nullable|date',
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
            'title.required'        => 'タイトルは必須です。',
            'title.max'             => 'タイトルは255文字以内で入力してください。',
            'author.required'       => '著者名は必須です。',
            'author.max'            => '著者名は255文字以内で入力してください。',
            'isbn.digits'           => 'ISBNは13桁の数字で入力してください。',
            'isbn.unique'           => 'このISBNは既に他の書籍で使用されています。',
            'published_date.date'   => '出版日は有効な日付形式で入力してください。',
            'genres.required'       => 'ジャンルは最低1つ選択してください。',
            'genres.array'          => 'ジャンルは配列形式で指定してください。',
            'genres.*.exists'       => '選択されたジャンルは無効です。',
        ];
    }
}
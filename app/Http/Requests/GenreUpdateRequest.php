<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ジャンル更新時のバリデーションクラス
 */
class GenreUpdateRequest extends FormRequest
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
        // ルートパラメータから対象のジャンルを取得
        $genre = $this->route('genre');
        $genreId = $genre instanceof \App\Models\Genre ? $genre->id : $genre;

        return [
            // 更新時は自分自身を除外して名前の一意性をチェック
            'name' => 'required|string|max:50|unique:genres,name,' . $genreId,
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
            'name.unique' => 'そのジャンル名は既に登録されています。',
        ];
    }
}
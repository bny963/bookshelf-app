<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ジャンル登録時のバリデーションクラス
 */
class GenreStoreRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:50|unique:genres,name',
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
            'name.required' => 'ジャンル名は必須です。',
            'name.max' => 'ジャンル名は50文字以内で入力してください。',
            'name.unique' => 'そのジャンル名は既に登録されています。',
        ];
    }
}
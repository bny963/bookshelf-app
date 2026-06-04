<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return ['name' => 'required|string|max:50|unique:genres,name'];
    }

    public function messages(): array
    {
        return ['name.unique' => 'そのジャンル名は既に登録されています。'];
    }
}
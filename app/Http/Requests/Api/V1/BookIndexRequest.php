<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BookIndexRequest extends FormRequest
{
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
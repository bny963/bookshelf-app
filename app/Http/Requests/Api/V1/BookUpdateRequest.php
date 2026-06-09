<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $bookId = $this->route('book');
        return [
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'nullable|digits:13|unique:books,isbn,' . $bookId,
            'published_date' => 'nullable|date',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
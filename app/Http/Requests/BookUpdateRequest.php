<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $book = $this->route('book');
        $bookId = $book instanceof \App\Models\Book ? $book->id : $book;

        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            // ID を正しく渡す
            'isbn' => 'nullable|digits:13|unique:books,isbn,' . $bookId,
            'published_date' => 'nullable|date',
            'genres' => 'required|array|min:1',
            'image_url' => 'nullable|url',
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.unique' => 'このISBNは他の書籍で使用されています。',
            'genres.required' => '少なくとも1つ以上のジャンルを選択してください。',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $genre = $this->route('genre');
        $genreId = $genre instanceof \App\Models\Genre ? $genre->id : $genre;

        return [
            'name' => 'required|string|max:255|unique:genres,name,' . $genreId,
        ];
    }
}
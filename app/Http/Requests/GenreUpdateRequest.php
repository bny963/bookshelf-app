<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $genreId = $this->route('genre');
        return ['name' => 'required|string|max:50|unique:genres,name,' . $genreId];
    }
}
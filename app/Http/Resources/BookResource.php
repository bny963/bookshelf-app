<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'genres' => $this->genres->map(fn($g) => [
                'id' => $g->id,
                'name' => $g->name
            ]),
            'average_rating' => $this->reviews_avg_rating ? (float) number_format($this->reviews_avg_rating, 1) : null,
            'reviews_count' => (int) $this->reviews_count,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
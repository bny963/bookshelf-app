<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 書籍データのリソース変換クラス
 */
class BookResource extends JsonResource
{
    /**
     * リソースを配列へ変換
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'genres' => $this->whenLoaded('genres', fn() => $this->genres->map(fn($g) => [
                'id' => $g->id,
                'name' => $g->name,
            ])),
            'average_rating' => $this->reviews_avg_rating ? (float) number_format($this->reviews_avg_rating, 1) : null,
            'reviews_count' => (int) ($this->reviews_count ?? 0),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
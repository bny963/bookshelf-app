<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GenreResource;
use App\Http\Resources\ReviewResource;

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
            'id'             => $this->id,
            'title'          => $this->title,
            'author'         => $this->author,
            'isbn'           => $this->isbn,
            'published_date' => $this->published_date?->format('Y-m-d'),
            'description'    => $this->description,
            'image_url'      => $this->image_url,
            'genres'         => GenreResource::collection($this->whenLoaded('genres')),
            'reviews'        => ReviewResource::collection($this->whenLoaded('reviews')),
            'average_rating' => $this->reviews_avg_rating !== null ? (float) number_format($this->reviews_avg_rating, 1) : null,
            'reviews_count'  => (int) ($this->reviews_count ?? 0),
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'user_name'  => $this->whenLoaded('user', fn() => $this->user->name),
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}

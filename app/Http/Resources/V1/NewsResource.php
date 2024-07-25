<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => env("DO_SPACES_IMAGES") . $this->image,
            'user' => new UserResource($this->user),
            'images' => NewsImageResource::collection($this->images),
            'videos' => NewsVideoResource::collection($this->videos),
            'state' => [
                'id' => $this->state->id,
                'name' => $this->state->name
            ]
        ];
    }
}

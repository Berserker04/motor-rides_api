<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'isDeleted' => $this->isDeleted == "N" ? "Activo" : "Eliminado",
            'category_id' => $this->category_id,
            'products' => ProductResource::collection($this->products),
        ];
    }
}

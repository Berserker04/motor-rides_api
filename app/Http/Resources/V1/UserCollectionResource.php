<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollectionResource extends JsonResource
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
            'fullName' => $this->fullName,
            'email' => $this->email,
            'photo' => $this->photo,
            'employee' => [
                'id' => $this->employee->id,
                'document' => $this->employee->document,
                'cellPhone' => $this->employee->cellPhone,
                'position' => $this->employee->position->name,
            ],
            'role' => [
                'id' => $this->role->id,
                'name' => $this->role->name
            ],
            'state' => [
                'id' => $this->state->id,
                'name' => $this->state->name
            ],
        ];
    }
}

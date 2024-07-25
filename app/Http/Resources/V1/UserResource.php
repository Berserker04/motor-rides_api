<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'photo' => env("DO_SPACES_PHOTOS")  . $this->photo,
            'employee' => new EmployeeResource($this->employee),
            'role' => [
                'id' => $this->role->id,
                'name' => $this->role->name
            ]
        ];
    }
}

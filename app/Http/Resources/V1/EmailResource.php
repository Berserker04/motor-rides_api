<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailResource extends JsonResource
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
            'cellPhone' => $this->cellPhone,
            'subject' => $this->subject,
            'message' => $this->message,
            'state' => [
                'id' => $this->state->id,
                'name' => $this->state->name
            ],
        ];
    }
}

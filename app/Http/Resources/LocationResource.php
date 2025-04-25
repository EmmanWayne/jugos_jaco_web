<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "plus_code" => $this->plus_code,
            "model_id"  => $this->model_id, 
        ];
    }
}

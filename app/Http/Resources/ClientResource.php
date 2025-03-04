<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'department' => $this->department,
            'township' => $this->township,
            'location' => [
                'latitude' => $this->location?->latitude,
                'longitude' => $this->location?->longitude,
            ],
            'type_price' => $this->typePrice?->name,
        ];
    }
}

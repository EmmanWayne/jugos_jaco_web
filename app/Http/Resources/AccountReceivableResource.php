<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountReceivableResource extends JsonResource
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
            'client_name' => $this->sale->client->full_name ?? $this->name,
            'business_name' => $this->sale->client->bussines_name ?? $this->name,
            'total_amount' => (float) $this->total_amount,
            'remaining_balance' => (float) $this->remaining_balance,
            'due_date' => Carbon::parse($this->due_date)->format('Y-m-d'),
            'status' => $this->status->getLabel()
        ];
    }
}

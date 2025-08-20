<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'client_name' => "{$this->client->first_name} {$this->client->last_name}",
            'employee_name' => "{$this->employee->first_name} {$this->employee->last_name}",
            'sale_date' => Carbon::parse($this->sale_date)->format('Y-m-d'),
            'cash_amount' => $this->cash_amount,
            'payment_reference' => $this->payment_reference,
            'notes' => $this->notes,
            'payment_type' => $this->payment_type->getLabel(),
            'subtotal' => $this->subtotal,
            'total_amount' => $this->total_amount
        ];
    }
}

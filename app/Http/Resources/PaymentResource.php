<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'amount' => (float) $this->amount,
            'balance_after_payment' => (float) $this->balance_after_payment,
            'payment_date' => Carbon::parse($this->payment_date)->format('Y-m-d'),
            'payment_method' => $this->payment_method->getLabel()
        ];
    }
}

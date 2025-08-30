<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountReceivableDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
        public function toArray(Request $request): array
        {
            $detail = (new AccountReceivableResource($this))->toArray($request);
            return array_merge(
                $detail,
                [
                    'payments' => PaymentResource::collection($this->whenLoaded('payments')),
                ]
            );
        }
}

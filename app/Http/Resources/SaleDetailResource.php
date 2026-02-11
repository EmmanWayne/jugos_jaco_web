<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'header' => (new SaleResource($this))->toArray($request),
            'details' => $this->saleDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name,
                    'product_code' => $detail->product_code,
                    'unit_name' => $detail->unit_name,
                    'unit_abbreviation' => $detail->unit_abbreviation,
                    'quantity' => (float)$detail->quantity,
                    'tax_category_name' => $detail->tax_category_name,
                    'tax_rate' => (float)$detail->tax_rate,
                    'line_subtotal' => (float)$detail->line_subtotal,
                    'line_tax_amount' => (float)$detail->line_tax_amount,
                    'line_total' => (float)$detail->line_total,
                    'price_include_tax' => (bool)$detail->price_include_tax,
                    'discount_percentage' => (float)$detail->discount_percentage,
                    'discount_amount' => (float)$detail->discount_amount,
                ];
            }),
        ];
    }
}

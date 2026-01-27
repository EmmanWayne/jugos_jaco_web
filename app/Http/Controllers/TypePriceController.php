<?php

namespace App\Http\Controllers;

use App\Models\TypePrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class TypePriceController extends Controller
{
    use ApiResponse;

    /**
     * Get all available type prices.
     *
     * @return JsonResponse
     */
    public function GetTypePrices(): JsonResponse
    {
        try {
            $typePrices = TypePrice::select('id', 'name')
            ->orderBy('name')
            ->get();
            return $this->successResponse($typePrices);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 500, 'Error al obtener los precios de lista.');
        }
    }
}

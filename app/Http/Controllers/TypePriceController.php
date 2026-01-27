<?php

namespace App\Http\Controllers;

use App\Models\TypePrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TypePriceController extends Controller
{
    /**
     * Get all available type prices.
     *
     * @return JsonResponse
     */
    public function GetTypePrices(): JsonResponse
    {
        $typePrices = TypePrice::select('id', 'name')
        ->orderBy('name')
        ->get();

        return response()->json($typePrices);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssignedProductResource;
use App\Models\AssignedProduct;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssignedProductController extends Controller
{
    use ApiResponse;
    /**
     * Obtener los productos asignados al empleado asociado al usuario autenticado para la fecha actual.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getProductAssigned(Request $request)
    {
        try {
            $assignedProducts = AssignedProduct::todayAssignments()
                ->with(['details.product'])
                ->where('employee_id', Auth::user()->employee_id)
                ->first();
            
            if (!$assignedProducts) {
                throw new \Exception('No hay productos asignados para el empleado en la fecha actual.');
            }
            
            $details = $assignedProducts->details;
         
            return $this->successResponse(
                AssignedProductResource::collection($details),
                'Productos asignados obtenidos correctamente.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }
}

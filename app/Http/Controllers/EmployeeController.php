<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Traits\ApiResponse;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\LocationResource;
use Illuminate\Http\Request;
use Locale;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeeController extends Controller
{
    use ApiResponse;

    /**
     * Get employee by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployee($id)
    {
        try {
            $employee = Employee::with(['branch'])->find($id);
            
            if (!$employee) {
                throw new NotFoundHttpException('Empleado no encontrado');
            }
            
            return $this->successResponse(
                new EmployeeResource($employee),
                'Empleado obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e, 'Error al obtener el empleado');
        }
    }

    /**
     * Create employee location
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLocation($id, Request $request)
    {
        try {
            $employee = Employee::find($id);
            
            if (!$employee) {
                throw new NotFoundHttpException('Empleado no encontrado');
            }
            
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $location = $employee->locations()->create($validated);
            
            return $this->successResponse(
                new LocationResource($location),
                'Ubicación creada correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }
}

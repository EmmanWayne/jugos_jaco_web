<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Employee;
use App\Traits\ApiResponse;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\LocationResource;
use App\Services\PlusCodeService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmployeeController extends Controller
{
    use ApiResponse;

    protected $plusCodeService;

    public function __construct()
    {
        $this->plusCodeService = new PlusCodeService();
    }

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
                throw new NotFoundHttpException('Empleado no encontrado', null, 404);
            }
            
            return $this->successResponse(
                new EmployeeResource($employee),
                'Empleado obtenido correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e, $e->getCode());
        }
    }

    /**
     * Create employee location
     *
     * @param int $id
     * @param LocationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLocation($id, LocationRequest $request)
    {
        try {
            $employee = Employee::find($id);
            
            if (!$employee) {
                throw new NotFoundHttpException('Empleado no encontrado', null, 404);
            }

            $location = $employee->locations()->create([
                ...$request->validated(),
                'plus_code' => $this->plusCodeService->encode($request->latitude, $request->longitude)
            ]);
            
            return $this->successResponse(
                new LocationResource($location),
                'Ubicación creada correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e, $e->getCode());
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Obtener un empleado por ID
     *
     * @param int $id Employee ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployee($id)
    {
        try {
            $employee = Employee::with(['branch:id,name,address,phone_number'])->find($id);
            
            if (!$employee) {
                return response()->json([
                    'message' => 'Empleado no encontrado',
                ], 404);
            }
            
            // Return employee data
            return response()->json([
                'message' => 'Empleado obtenido correctamente',
                'employee' => $employee,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'message' => 'Error al obtener el empleado',
                'error' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    /**
     * Crear una ubicación para un empleado
     *
     * @param int $id Employee ID
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLocation($id, Request $request)
    {
        try {
            $employee = Employee::find($id);
            
            if (!$employee) {
                return response()->json([
                    'message' => 'Empleado no encontrado',
                ], 404);
            }
            
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $employee->locations()->create([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
            
            return response()->json([
                'message' => 'Ubicación creada correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la ubicación',
                'error' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }
}

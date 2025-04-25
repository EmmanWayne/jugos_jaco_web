<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use App\Models\Employee;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Login a user and return a token
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $employee = Employee::with('user')
                ->where('identity', $request->identity)
                ->first();

            if (!$employee) {
                throw ValidationException::withMessages([
                    'identity' => 'Las credenciales son incorrectas.'
                ]);
            }

            $this->validateUserStatus($employee);
            $this->validateUserRole($employee);

            if (!Hash::check($request->password, $employee->user->password)) {
                throw ValidationException::withMessages([
                    'identity' => 'Las credenciales son incorrectas.'
                ]);
            }

            $token = $employee->user->createToken($request->device_name)->plainTextToken;

            return $this->successResponse([
                'token' => $token,
                'token_type' => 'Bearer',
                'employee_id' => $employee->id,
            ], 'Inicio de sesión exitoso');
        } catch (ValidationException $e) {
            return $this->errorResponse($e, 401, $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse($e, $e->getCode(), 'Error al iniciar sesión');
        }
    }

    /**
     * Logout a user and delete the token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(
                null,
                'Sesión cerrada exitosamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e, $e->getCode(), 'Error al cerrar sesión');
        }
    }

    /**
     * Validate status of the user
     * 
     * `status` must be true
     */
    private function validateUserStatus(Employee $employee): void
    {
        if (!$employee->user->status) {
            throw ValidationException::withMessages([
                'status' => 'El usuario se encuentra inactivo.'
            ]);
        }
    }

    /**
     * Validate role of the user
     * 
     * `role` must be in the allowed roles
     */
    private function validateUserRole(Employee $employee): void
    {
        if (!$employee->user->hasAnyRole(UserRole::getAllowedRoles())) {
            throw ValidationException::withMessages([
                'role' => 'El usuario no tiene los permisos necesarios para acceder.'
            ]);
        }
    }
}

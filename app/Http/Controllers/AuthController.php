<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 
     * Metodo encargado de realizar el inicio de sesión
     * 
     * @param Request $request 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $employee = Employee::where('identity', $request->identity)->first();

        if (! $employee || ! Hash::check($request->password, $employee->user->password)) {
            throw ValidationException::withMessages([
                'identity' => ['Las credenciales son incorrectas.'],
            ]);
        }

        $token = $employee->user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'messaje' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Metodo encargado de realizar el registro de un usuario
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'employed_id' => 'required|exists:employees,id',
                'password' => 'required|string|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'employed_id' => $request->employed_id,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'messaje' => 'Usuario registrado exitosamente.',
                'user_id' => $user->id,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'messaje' => 'Ha ocurrido un error al registrar el usuario.',
            ], $e->getCode());
        }
    }

    /**
     * Método encargado de cerrar la sesión del usuario
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Sesión cerrada exitosamente.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'messaje' => 'Ha ocurrido un error al cerrar la sesión.',
            ], $e->getCode());
        }
    }
}

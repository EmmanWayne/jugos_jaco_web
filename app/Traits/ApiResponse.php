<?php

namespace App\Traits;

trait ApiResponse
{
    protected function errorResponse(\Exception $e, string $message = 'Error en la operación.', $code = 500)
    {
        return response()->json([
            'message' => $message,
            'error' => $e->getMessage(),
            'code' => $e->getCode() || $code
        ], 500);
    }

    protected function successResponse($data, string $message = 'Operación exitosa.', int $code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $code);
    }
}

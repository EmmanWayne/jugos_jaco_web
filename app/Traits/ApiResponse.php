<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Return an error response
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(\Exception $e, $statusCode = 500, string $message = 'Error en la operación.')
    {
        return response()->json([
            'message' => $message,
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ], $statusCode);
    }

    /**
     * Return a success response with data
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = 'Operación exitosa.', int $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }
}

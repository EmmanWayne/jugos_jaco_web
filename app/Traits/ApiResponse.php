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
        $code = $statusCode && $statusCode >= 100 && $statusCode < 600 ? $statusCode : 500;

        return response()->json([
            'message' => $message,
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ], $code);
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

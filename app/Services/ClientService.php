<?php

namespace App\Services;

use App\Models\Client;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService
{
    use ApiResponse;

    public function updatePosition(int $position, int $employeeId, int $clientId, string $day): JsonResponse
    {
        try {
            $client = Client::find($clientId);

            $comparisonOperator = ">=";
            $positionAdjustment = -1;

            if ($position < $client->position) {
                $comparisonOperator = "<=";
                $positionAdjustment = 1;
            }

            $clients = Client::visitDay($day)
                ->where('id', '!=', $clientId)
                ->where('position', $comparisonOperator, $position)
                ->where('employee_id', $employeeId)->get();

            Log::info("Clientes a actualizar: ", [
                'day' => $day,
                'employee_id' => $employeeId,
                'position' => $position,
                'comparisonOperator' => $comparisonOperator,
                'positionAdjustment' => $positionAdjustment,
                'clients' => $clients->toArray()
            ]);

            if ($clients->isEmpty()) return $this->errorResponse(throw new NotFoundHttpException("No hay clientes que visitar este día."), 404);

            $client->update([
                'position' => $position
            ]);

            $clients->each(function ($client) use ($positionAdjustment) {
                $client->update([
                    'position' => $client->position + ($positionAdjustment)
                ]);
            });

            return $this->successResponse(null, "Posición actualizada con éxito", 200);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e, 40);
        } catch (\Exception $e) {
            return $this->errorResponse($e, 500);
        }
    }
}

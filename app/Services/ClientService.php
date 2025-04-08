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

    public function updatePosition(int $position, int $employeeId, string $day, ?int $clientId = null): JsonResponse
    {
        try {
            $client = null;
            $positionAdjustment = 1;
            $clients = null;

            if (!is_null($clientId) && $client = Client::find($clientId)) {
                if ($position == $client->position) {
                    $clients = collect();
                } else {
                    $movingUp = $position < $client->position;
                    $rangePositionUpdate =  $movingUp ? [$position, $client->position - 1]: [$client->position + 1, $position];
                    $positionAdjustment = $movingUp ? 1 : -1;

                    $clients = Client::visitDay($day)
                        ->where('id', '!=', $clientId)
                        ->where('employee_id', $employeeId)
                        ->whereBetween('position', $rangePositionUpdate)
                        ->get();
                }
            } else {
                $clients = Client::visitDay($day)
                    ->where('position', '>=', $position)
                    ->where('employee_id', $employeeId)
                    ->get();
            }

            if (isset($client)) {
                $client->update([
                    'position' => $position
                ]);
            }

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

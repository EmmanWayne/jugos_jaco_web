<?php

namespace App\Services;

use App\Enums\StoragePath;
use App\Models\Client;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientService
{
    use ApiResponse;

    /**
     * Update the position of a client.
     *
     * @param int $position
     * @param int $employeeId
     * @param string $day
     * @param int|null $clientId
     * @return JsonResponse
     */
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

    /**
     * Delete the business images of a client.
     *
     * @param int $clientId
     * @return void
     */
    public function deleteBusinessImages(int $clientId): void
    {
        try {
            $client = Client::findOrFail($clientId);

            if ($client->businessImages) {
                foreach ($client->businessImages as $image) {
                    if (Storage::disk(StoragePath::ROOT_DIRECTORY->value)->exists($image->path)) {
                        Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($image->path);
                    }
                }

                $client->businessImages()->delete();
            }
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Cliente no encontrado');
        } catch (\Exception $e) {
            throw new \Exception('Ha ocurrido un error al borrar la imagen de negocio');
        }
    }

    /**
     * Delete the profile image of a client.
     *
     * @param int $clientId
     * @return void
     */
    public function deleteProfileImage(int $clientId): void
    {
        try {
            $client = Client::findOrFail($clientId);

            if ($client->profileImage) {
                if (Storage::disk(StoragePath::ROOT_DIRECTORY->value)->exists($client->profileImage->path)) {
                    Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($client->profileImage->path);
                }

                $client->profileImage()->delete();
            }
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Cliente no encontrado');
        } catch (\Exception $e) {
            throw new \Exception('Ha ocurrido un error al borrar la imagen de perfil');
        }
    }

    /**
     * Delete all images of a client.
     * 
     * @param int $clientId
     * @return void
     */
    public function deleteClientImages(int $clientId): void
    {
        try {

            $this->deleteBusinessImages($clientId);
            $this->deleteProfileImage($clientId);
        
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Cliente no encontrado');
        } catch (\Exception $e) {
            throw new \Exception('Ha ocurrido un error al borrar el cliente');
        }
    }
}

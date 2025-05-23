<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientVisitDay;
use App\Http\Requests\ClientVisitDayRequest;
use App\Http\Resources\ClientVisitDayResource;
use App\Traits\ApiResponse;
use App\Services\ClientService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientVisitDayController extends Controller
{
    use ApiResponse;

    private $clientService;

    public function __construct()
    {
        $this->clientService = new ClientService();
    }

    /**
     * Get all visit days for a client.
     *
     * @param int $clientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisitDays($clientId)
    {
        try {
            $client = Client::findOrFail($clientId);

            $this->validateClientEmployee($clientId);

            $visitDays = $client->visitDays()
                ->orderBy('visit_day')
                ->orderBy('position')
                ->get();

            return $this->successResponse(
                ClientVisitDayResource::collection($visitDays),
                'Días de visita obtenidos correctamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e, 404, 'Cliente no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Get a single visit day.
     *
     * @param int $clientId
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVisitDayById($clientId, $id)
    {
        try {
            $client = Client::findOrFail($clientId);

            $this->validateClientEmployee($clientId);

            $visitDay = $client->visitDays()->findOrFail($id);

            return $this->successResponse(
                new ClientVisitDayResource($visitDay),
                'Día de visita obtenido correctamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e, 404, 'Día de visita no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Create a new visit day for a client.
     *
     * @param ClientVisitDayRequest $request
     * @param int $clientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createVisitDay(ClientVisitDayRequest $request, $clientId)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($clientId);

            $this->validateClientEmployee($clientId);

            $this->validateExistsVisitDay($clientId, $request->visit_day);

            $this->clientService->updatePosition(
                $request->position,
                Auth::user()->employee_id,
                $request->visit_day
            );

            $visitDay = $client->visitDays()->create([
                'position' => $request->position,
                'visit_day' => $request->visit_day
            ]);

            DB::commit();

            return $this->successResponse(
                new ClientVisitDayResource($visitDay),
                'Día de visita creado correctamente',
                201
            );
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->errorResponse($e, 404, 'Cliente no encontrado');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    /**
     * Reorder visit days for a client.
     *
     * @param ClientVisitiDayRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderVisitDays($client_id, ClientVisitDayRequest $request)
    {
        return $this->clientService->updatePosition(
            $request->position,
            Auth::user()->employee_id,
            $request->visit_day,
            $client_id
        );
    }

    /**
     * Delete a visit day.
     *
     * @param int $clientId
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteVisitDay($clientId, $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($clientId);

            $this->validateClientEmployee($clientId);

            $visitDay = $client->visitDays()->findOrFail($id);
            $this->clientService->updatePositionAfterDeleteVisitDay(
                $visitDay->position,
                Auth::user()->employee_id,
                $visitDay->visit_day
            );

            $visitDay->delete();

            DB::commit();

            return $this->successResponse(
                null,
                'Día de visita eliminado correctamente'
            );
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->errorResponse($e, 404, 'Día de visita o cliente no encontrado');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    /**
     * Validate if the authenticated user is the employee of the client.
     *
     * @param int $clientId
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function validateClientEmployee($clientId)
    {
        $client = Client::findOrFail($clientId);

        if (Auth::user()->employee_id != $client->employee_id) {
            throw new \Exception('No tienes permiso para acceder a este cliente');
        }
    }

    /**
     * Validate if the visit day already exists for the client.
     *
     * @param int $clientId
     * @param string $visitDay
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function validateExistsVisitDay($clientId, $visitDay)
    {
        $client = Client::findOrFail($clientId);

        $existingVisitDay = $client->visitDays()
            ->where('visit_day', $visitDay)
            ->first();

        if (isset($existingVisitDay)) {
            throw new \Exception('El día de visita ya existe para este cliente');
        }
    }
}

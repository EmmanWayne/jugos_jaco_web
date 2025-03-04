<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use ApiResponse;

    public function getClients()
    {
        try {
            $clients = Client::where('employee_id', Auth::user()->id)
                ->with(['location:model_id,latitude,longitude', 'typePrice:id,name'])
                ->get();

            return $this->successResponse(
                ClientResource::collection($clients),
                'Clientes obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function createClient(ClientRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->validateExistingClient($request);

            $client = Client::create([
                ...$request->validated(),
                'employee_id' => Auth::user()->id
            ]);

            if ($request->filled(['latitude', 'longitude'])) {
                $client->location()->create([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                ]);
            }

            DB::commit();

            $client->load(['location', 'typePrice']);
            
            return (new ClientResource($client))
                ->additional(['message' => 'Cliente creado correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    public function updateClient(ClientRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);
            
            $this->validateExistingClient($request, $id);

            $client->update($request->validated());

            if ($request->filled(['latitude', 'longitude'])) {
                $client->location()->updateOrCreate(
                    ['model_id' => $client->id],
                    [
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude
                    ]
                );
            }

            $client->load(['location', 'typePrice']);

            DB::commit();

            return (new ClientResource($client))
                ->additional(['message' => 'Cliente actualizado correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    private function validateExistingClient($request, $client_id = null)
    {
        $query = Client::where('employee_id', Auth::user()->id)
            ->where('last_name', $request->last_name)
            ->where('first_name', $request->first_name)
            ->where('phone_number', $request->phone_number);

        if ($client_id) {
            $query->where('id', '!=', $client_id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'message' => 'Ya existe un cliente con el mismo nombre y número telefónico.'
            ]);
        }
    }
}

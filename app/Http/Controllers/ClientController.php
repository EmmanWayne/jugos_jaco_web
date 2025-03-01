<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Location;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    /**
     * Metodo encargado de obtener los clientes
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClients()
    {
        try {
            $clients = Client::where('employee_id', Auth::user()->id)
                ->with(['location:model_id,latitude,longitude', 'typePrice:id,name'])
                ->get();

            return response()->json([
                'clients' => $clients
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los clientes.',
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }
    }

    /**
     * Metodo encargado de crear un cliente
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createClient(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'address' => 'required|string',
                'phone_number' => 'required|string',
                'department' => 'required|string',
                'township' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
            ]);

            $this->validateExistingClient($request);

            $client = new Client();
            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->address = $request->address;
            $client->phone_number = $request->phone_number;
            $client->department = $request->department;
            $client->township = $request->township;
            $client->employee_id = Auth::user()->id;
            $client->save();

            $location = new Location();
            $location->latitude = $request->latitude;
            $location->longitude = $request->longitude;
            $client->location()->save($location);

            DB::commit();

            $client->load(['location:model_id,latitude,longitude', 'typePrice:id,name']);

            return response()->json([
                'message' => 'Cliente creado correctamente.',
                'client' => $client
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el cliente.',
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }
    }

    /**
     * Metodo encargado de actualizar un cliente
     * 
     * @param Request $request
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClient(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $client = Client::find($id);

            if (!$client) {
                throw ValidationException::withMessages([
                    'client' => 'Cliente no encontrado.'
                ]);
            }

            $this->validateExistingClient($request, $id);

            $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'address' => 'required|string',
                'phone_number' => 'required|string',
                'department' => 'required|string',
                'township' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
            ]);

            $client->first_name = $request->first_name;
            $client->last_name = $request->last_name;
            $client->address = $request->address;
            $client->phone_number = $request->phone_number;
            $client->department = $request->department;
            $client->township = $request->township;
            $client->save();

            $location = $client->location;
            $location->latitude = $request->latitude;
            $location->longitude = $request->longitude;
            $location->save();

            $client->load(['location:model_id,latitude,longitude', 'typePrice:id,name']);

            DB::commit();

            return response()->json([
                'message' => 'Cliente actualizado correctamente.',
                'client' => $client
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el cliente.',
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }
    }

    /**
     * Metodo encargado de validar si ya existe un cliente con el mismo nombre y número telefónico
     */
    private function validateExistingClient($request, $client_id = null)
    {
        $existingClient = Client::when($client_id, function ($query) use ($client_id) {
                $query->where('id', '!=', $client_id);
            })
            ->where([['last_name', $request->last_name], ['first_name', $request->first_name], ['phone_number', $request->phone_number], ['employee_id', Auth::user()->id]])
            ->first();

        if ($existingClient) {
            throw ValidationException::withMessages([
                'client' => 'Ya existe un cliente con el mismo nombre y número telefónico.'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\StoragePath;
use App\Models\Client;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ClientImageResource;
use App\Http\Resources\ClientResource;
use App\Services\PlusCodeService;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use ApiResponse;

    protected $plusCodeService;

    public function __construct()
    {
        $this->plusCodeService = new PlusCodeService();
    }

    /**
     * Get clients of the authenticated employee.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClients()
    {
        try {
            $clients = Client::where('employee_id', Auth::user()->id)
                ->with(['location', 'typePrice'])
                ->get();

            return $this->successResponse(
                ClientResource::collection($clients),
                'Clientes obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Create a new client.
     * 
     * @param ClientRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
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
                    'longitude' => $request->longitude,
                    'plus_code' => $this->plusCodeService->encode($request->latitude, $request->longitude)
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

    /**
     * Update a client.
     * 
     * @param ClientRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClient(ClientRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);

            $this->validateExistingClient($request, $id);

            $client->update($request->validated());

            if ($request->filled(['latitude', 'longitude'])) {
                $client->location()->updateOrCreate(
                    [
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'plus_code' => $this->plusCodeService->encode($request->latitude, $request->longitude)
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

    /**
     * Upload a business image of the client.
     * 
     * @param ImageReuest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadBusinessImage(ImageRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $timestamp = time();
                $fileName = "{$timestamp}_{$id}.{$extension}";
                $path = $image->storeAs(StoragePath::CLIENTS_BUSINESS_IMAGES->value, $fileName, StoragePath::ROOT_DIRECTORY->value);

                $client->businessImages()->create([
                    'path' => $path,
                    'type' => 'business',
                ]);
            }

            DB::commit();

            return $this->successResponse(
                null,
                'Imagen del cliente agregada correctamente'
            );
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->errorResponse($e, 404, "Cliente no encontrado.");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e, $e->getCode(), "Error al subir la imagen.");
        }
    }

    /**
     * Upload a profile image of the client.
     * 
     * @param ImageRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProfileImage(ImageRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);

            if ($request->hasFile('image')) {
                // Eliminar foto de perfil anterior si existe
                if ($client->profileImage) {
                    Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($client->profileImage->path);
                    $client->profileImage->delete();
                }

                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $timestamp = time();
                $fileName = "{$timestamp}_{$id}.{$extension}";
                $path = $image->storeAs(StoragePath::CLIENTS_PROFILE_IMAGE->value, $fileName, StoragePath::ROOT_DIRECTORY->value);

                $client->profileImage()->create([
                    'path' => $path,
                    'type' => 'profile',
                ]);
            }

            DB::commit();

            $client->load('profileImage');

            return $this->successResponse(
                new ClientImageResource($client->profileImage),
                'Imagen de perfil actualizada correctamente'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->errorResponse($e, 404, 'Cliente no encontrado');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e, $e->getCode(), 'Error al subir la imagen de perfil del cliente.');
        }
    }

    /**
     * Get business images of the client.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImagesBusiness($id)
    {
        try {
            $client = Client::findOrFail($id);

            return $this->successResponse(
                ClientImageResource::collection($client->businessImages),
                'Imágenes del cliente obtenidas correctamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e, 404, 'Cliente no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Validate if there is an existing client with the same name and phone number.
     */
    private function validateExistingClient($request, $client_id = null)
    {
        $query = Client::where('last_name', $request->last_name)
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

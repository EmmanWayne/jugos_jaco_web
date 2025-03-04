<?php

namespace App\Filament\Pages\Locations;

use Filament\Pages\Page;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class ClientLocations extends Page
{
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationGroup = 'Ubicaciones';
    protected static ?string $navigationLabel = 'Ubicaciones Clientes';
    protected static ?string $title = 'Mapa de Clientes';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.locations.client-locations';

    public function getViewData(): array
    {
        // Obtener todos los empleados activos
        $employees = \App\Models\Employee::select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => "{$employee->first_name} {$employee->last_name}"
                ];
            });

        // Optimizar la consulta incluyendo la relación con empleado
        $clients = Client::select('id', 'first_name', 'last_name', 'address', 'latitude', 'longitude', 'employee_id')
            ->with(['employee:id,first_name,last_name']) // Cargar solo los campos necesarios del empleado
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($client) {
                $fullName = "{$client->first_name} {$client->last_name}";
                $googleMapsUrl = "https://www.google.com/maps?q={$client->latitude},{$client->longitude}";

                $whatsappMessage = urlencode("*Información del Cliente*\n" .
                    "📋 Nombre: {$fullName}\n" .
                    "📍 Dirección: {$client->address}\n" .
                    "📌 Ubicación: {$googleMapsUrl}\n" .
                    "👤 Empleado Asignado: " . ($client->employee ? "{$client->employee->first_name} {$client->employee->last_name}" : "Sin asignar"));

                return [
                    'id' => $client->id,
                    'nombres' => $client->first_name,
                    'apellidos' => $client->last_name,
                    'direccion' => $client->address,
                    'latitude' => $client->latitude,
                    'longitude' => $client->longitude,
                    'coordenadas' => "Lat: {$client->latitude}, Long: {$client->longitude}",
                    'empleado_asignado' => $client->employee
                        ? "{$client->employee->first_name} {$client->employee->last_name}"
                        : 'Sin empleado asignado',
                    'employee_id' => $client->employee_id,
                    'whatsapp_share' => "https://wa.me/?text={$whatsappMessage}",
                    'maps_url' => $googleMapsUrl,
                ];
            });

        Log::info('Clientes cargados:', ['count' => $clients->count()]);

        return [
            'clients' => $clients,
            'employees' => $employees,
        ];
    }
}

<?php

namespace App\Filament\Pages\Locations;

use Filament\Pages\Page;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class ClientLocations extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-map-pin';
    protected static ?string $navigationLabel = 'Ubicaciones de Clientes';
    protected static ?string $title = 'Mapa de Clientes';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.locations.client-locations';

    public function getViewData(): array
    {
        // Obtener todos los clientes, incluso sin ubicación
        $allClients = Client::with(['location', 'employee'])
            ->get()
            ->map(function ($client) {
                $data = [
                    'id' => $client->id,
                    'nombre' => $client->full_name,
                    'direccion' => $client->address,
                    'empleado' => optional($client->employee)->full_name ?? 'Sin asignar',
                    'employee_id' => $client->employee_id,
                    'has_location' => $client->location !== null
                ];

                // Agregar datos de ubicación si existen
                if ($client->location) {
                    $data['location'] = [
                        'lat' => $client->location->latitude,
                        'lng' => $client->location->longitude,
                        'maps_url' => "https://www.google.com/maps?q={$client->location->latitude},{$client->location->longitude}",
                        'whatsapp_url' => $this->generateWhatsAppLink($client)
                    ];
                }

                return $data;
            });

        // Obtener todos los empleados
        $employees = Employee::orderBy('first_name')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'nombre' => $employee->full_name
                ];
            });

        return [
            'clients' => $allClients,
            'employees' => $employees,
            'center' => [
                'lat' => 14.6349,
                'lng' => -86.9315
            ]
        ];
    }

    private function generateWhatsAppLink($client): string
    {
        $mapsUrl = "https://www.google.com/maps?q={$client->location->latitude},{$client->location->longitude}";
        $message = "*Información del Cliente*\n" .
            "📋 Nombre: {$client->full_name}\n" .
            "📍 Dirección: {$client->address}\n" .
            "📌 Ubicación: {$mapsUrl}\n" .
            "👤 Empleado: " . (optional($client->employee)->full_name ?? "Sin asignar");

        return "https://wa.me/?text=" . urlencode($message);
    }
}

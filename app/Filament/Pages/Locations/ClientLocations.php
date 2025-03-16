<?php

namespace App\Filament\Pages\Locations;

use Filament\Pages\Page;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClientLocations extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-map-pin';
    protected static ?string $navigationLabel = 'Ubicaciones';
    protected static ?string $title = 'Mapa de ubicaciones';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.locations.client-locations';

    public function getViewData(): array
    {
        return [
            'clients' => $this->getClientsData(),
            'employeeLocations' => $this->getEmployeeLocationsData(),
            'statistics' => $this->getStatistics(),
            'center' => [
                'lat' => 14.6349,
                'lng' => -86.9315
            ]
        ];
    }

    private function getClientsData()
    {
        return Client::with(['location', 'employee'])
            ->get()
            ->map(function ($client) {
                $data = [
                    'id' => $client->id,
                    'tipo' => 'cliente',
                    'nombre' => $client->full_name,
                    'direccion' => $client->address,
                    'department' => $client->department,
                    'township' => $client->township,
                    'phone_number' => $client->phone_number,
                    'empleado' => optional($client->employee)->full_name ?? 'Sin asignar',
                    'employee_id' => $client->employee_id,
                    'has_location' => $client->location !== null
                ];

                if ($client->location) {
                    $data['location'] = [
                        'lat' => $client->location->latitude,
                        'lng' => $client->location->longitude,
                        'maps_url' => $this->generateGoogleMapsUrl($client->location),
                        'whatsapp_url' => $this->generateWhatsAppLink($client)
                    ];
                }

                return $data;
            });
    }

    private function getEmployeeLocationsData()
    {
        return Employee::with(['locations', 'branch'])
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'nombre' => $employee->full_name,
                    'phone_number' => $employee->phone_number,
                    'identity' => $employee->identity,
                    'address' => $employee->address,
                    'branch_name' => $employee->branch ? $employee->branch->name : 'Sin sucursal',
                    'has_routes' => $employee->locations->isNotEmpty(),
                    'last_location' => $employee->locations->first() ? [
                        'timestamp' => $employee->locations->first()->created_at->format('Y-m-d H:i:s'),
                        'date' => $employee->locations->first()->created_at->format('Y-m-d')
                    ] : null,
                    'locations' => $employee->locations
                        ->map(function ($location) {
                            return [
                                'lat' => $location->latitude,
                                'lng' => $location->longitude,
                                'timestamp' => $location->created_at->format('Y-m-d H:i:s'),
                                'date' => $location->created_at->format('Y-m-d'),
                                'maps_url' => $this->generateGoogleMapsUrl($location)
                            ];
                        })
                ];
            });
    }

    private function getStatistics()
    {
        // Estadísticas de clientes
        $totalClients = Client::count();
        $clientsWithLocation = Client::has('location')->count();

        // Estadísticas de empleados
        $totalEmployees = Employee::count();
        $employeesWithLocations = Employee::has('locations')->count();
        
        // Empleados activos hoy (con registros de ubicación del día actual)
        $activeToday = Employee::whereHas('locations', function ($query) {
            $query->whereDate('created_at', Carbon::today());
        })->count();

        return [
            'clients' => [
                'total' => $totalClients,
                'with_location' => $clientsWithLocation,
            ],
            'employees' => [
                'total' => $totalEmployees,
                'with_locations' => $employeesWithLocations,
                'active_today' => $activeToday,
                'inactive_today' => $totalEmployees - $activeToday
            ]
        ];
    }

    private function generateGoogleMapsUrl($location): string
    {
        return "https://www.google.com/maps?q={$location->latitude},{$location->longitude}";
    }

    private function generateWhatsAppLink($client): string
    {
        $mapsUrl = $this->generateGoogleMapsUrl($client->location);
        $message = "*Información del Cliente*\n" .
            "Nombre: {$client->full_name}\n" .
            "Dirección: {$client->address}\n" .
            "Departamento: {$client->department}\n" .
            "Municipio: {$client->township}\n" .
            "Teléfono: {$client->phone_number}\n" .
            "Ubicación: {$mapsUrl}\n" .
            "Empleado Asignado: " . (optional($client->employee)->full_name ?? "Sin asignar");

        return "https://wa.me/?text=" . urlencode($message);
    }

    public function filterClientById($clientId)
    {
        return Client::with(['location', 'employee'])
            ->where('id', $clientId)
            ->first()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'nombre' => $client->full_name,
                    'direccion' => $client->address,
                    'empleado' => optional($client->employee)->full_name ?? 'Sin asignar',
                    'has_location' => $client->location !== null,
                    'location' => $client->location ? [
                        'lat' => $client->location->latitude,
                        'lng' => $client->location->longitude,
                        'maps_url' => $this->generateGoogleMapsUrl($client->location),
                    ] : null,
                ];
            });
    }

    public function filterEmployeeById($employeeId)
    {
        return Employee::with(['locations' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->where('id', $employeeId)
        ->first()
        ->map(function ($employee) {
            return [
                'id' => $employee->id,
                'nombre' => $employee->full_name,
                'locations' => $employee->locations->map(function ($location) {
                    return [
                        'lat' => $location->latitude,
                        'lng' => $location->longitude,
                        'timestamp' => $location->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ];
        });
    }
}
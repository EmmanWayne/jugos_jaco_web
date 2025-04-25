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
    protected static ?string $navigationGroup = 'Administración';

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
        return Client::withLocationData()
            ->get()
            ->map->mapData;
    }

    private function getEmployeeLocationsData()
    {
        return Employee::withRouteData()
            ->get()
            ->map->mapData;
    }

    private function getStatistics(): array
    {
        $clientStats = [
            'total' => Client::count(),
            'with_location' => Client::has('location')->count(),
        ];

        $employeeStats = [
            'total' => Employee::count(),
            'with_locations' => Employee::has('locations')->count(),
            'active_today' => Employee::activeToday()->count(),
        ];

        return [
            'clients' => $clientStats,
            'employees' => [
                ...$employeeStats,
                'inactive_today' => $employeeStats['total'] - $employeeStats['active_today']
            ]
        ];
    }

    public function filterClientById($clientId)
    {
        return Client::withLocationData()
            ->findOrFail($clientId)
            ->mapData;
    }

    public function filterEmployeeById($employeeId)
    {
        return Employee::withRouteData()
            ->findOrFail($employeeId)
            ->mapData;
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }
}

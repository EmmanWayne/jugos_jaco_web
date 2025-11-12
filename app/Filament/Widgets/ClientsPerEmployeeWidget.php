<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class ClientsPerEmployeeWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Clientes por Empleado';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isLazy = true;
    public static function canView(): bool
    {
        $user = Auth::user();
        return UserRole::canUserViewWidget($user, static::class);
    }
    public function mount(): void
    {
        if (! UserRole::canUserViewWidget(Auth::user(), static::class)) {
            abort(403);
        }
    }
    
    protected function getData(): array
    {
        $employees = Employee::withCount('clients')
            ->orderBy('clients_count', 'desc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Clientes Asignados',
                    'data' => $employees->pluck('clients_count')->toArray(),
                    'backgroundColor' => [
                        '#10B981', // green-500
                        '#3B82F6', // blue-500
                        '#F59E0B', // amber-500
                        '#EF4444', // red-500
                        '#8B5CF6', // violet-500
                        '#06B6D4', // cyan-500
                        '#84CC16', // lime-500
                        '#F97316', // orange-500
                    ],
                    'borderColor' => '#374151',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $employees->pluck('full_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}

<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeClientsStatsWidget extends BaseWidget
{
    public ?Employee $record = null;

    protected function getStats(): array
    {
        $clientsCount = $this->record?->clients()->count() ?? 0;
        $recentClientsCount = $this->record?->clients()
            ->where('created_at', '>=', now()->subDays(30))
            ->count() ?? 0;

        return [
            Stat::make('Total de Clientes', $clientsCount)
                ->description('Clientes asignados a este empleado')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Clientes Recientes', $recentClientsCount)
                ->description('Nuevos clientes en los últimos 30 días')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
                
            Stat::make('Promedio por Mes', $this->getMonthlyAverage())
                ->description('Clientes promedio asignados por mes')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }

    private function getMonthlyAverage(): string
    {
        if (!$this->record) {
            return '0';
        }

        $clientsCount = $this->record->clients()->count();
        
        if ($clientsCount === 0) {
            return '0';
        }

        $firstClientDate = $this->record->clients()
            ->orderBy('created_at')
            ->first()?->created_at;

        if (!$firstClientDate) {
            return '0';
        }

        $monthsDiff = $firstClientDate->diffInMonths(now()) + 1;
        $average = round($clientsCount / $monthsDiff, 1);

        return (string) $average;
    }
}

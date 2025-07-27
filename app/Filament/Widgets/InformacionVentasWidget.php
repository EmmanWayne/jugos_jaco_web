<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Bill;

class InformacionVentasWidget extends BaseWidget
{
    protected static string $view = 'filament.widgets.informacion-ventas-widget';

    protected ?string $heading = 'Información de Ventas';
    protected ?string $description = 'Resumen de ventas del día, semana y mes.';

    protected function getCards(): array
    {
        $ventasHoy = Bill::whereDate('created_at', today())->sum('amount');
        $ventasSemana = Bill::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
        $ventasMes = Bill::whereMonth('created_at', now()->month)->sum('amount');

        return [
            Card::make('Ventas Hoy', '$' . number_format($ventasHoy, 2))
                ->icon('heroicon-o-currency-dollar')
                ->color('primary'),

            Card::make('Ventas Semana', '$' . number_format($ventasSemana, 2))
                ->icon('heroicon-o-calendar')
                ->color('secondary'),

            Card::make('Ventas Mes', '$' . number_format($ventasMes, 2))
                ->icon('heroicon-o-calendar-days')
                ->color('success'),
        ];
    }
}
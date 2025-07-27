<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Product;

class RegistrosDelSistemaWidget extends BaseWidget
{
    protected ?string $heading = 'Registros del Sistema';
    protected ?string $description = 'Resumen de la cantidad total de clientes, empleados y productos registrados en el sistema.';

    protected static string $view = 'filament.widgets.registros-del-sistema-widget';

    protected function getCards(): array
    {
        return [
            Card::make('Clientes', Client::count())
                ->icon('heroicon-o-users')
                ->color('success'),

            Card::make('Empleados', Employee::count())
                ->icon('heroicon-o-briefcase')
                ->color('warning'),

            Card::make('Productos', Product::count())
                ->icon('heroicon-o-shopping-cart')
                ->color('info'),
        ];
    }
}
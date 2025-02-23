<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Product;

class StatsOverview extends BaseWidget
{
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

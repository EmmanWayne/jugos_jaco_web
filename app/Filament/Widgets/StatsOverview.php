<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\AccountReceivable;
use App\Models\FinishedProductInventory;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected function getStats(): array
    {
        // Ventas del mes actual
        $currentMonthSales = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');
            
        // Cuentas por cobrar vencidas
        $overdueAccounts = AccountReceivable::where('status', 'pending')
            ->where('due_date', '<', now())
            ->sum('remaining_balance');
            
        // Productos con stock crítico
        $criticalStock = FinishedProductInventory::whereColumn('stock', '<=', 'min_stock')
            ->count();
            
        // Empleado destacado del mes
        $topEmployee = Employee::select('employees.first_name', 'employees.last_name')
            ->leftJoin('sales', 'employees.id', '=', 'sales.employee_id')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->groupBy('employees.id', 'employees.first_name', 'employees.last_name')
            ->orderBy(DB::raw('SUM(sales.total_amount)'), 'desc')
            ->first();

        return [
            Stat::make('Ventas del Mes', 'L.' . number_format($currentMonthSales, 0))
                ->description('Ingresos del mes actual')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Cuentas Vencidas', 'L>' . number_format($overdueAccounts, 0))
                ->description('Monto pendiente de cobro')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueAccounts > 0 ? 'danger' : 'success'),
                
            Stat::make('Stock Crítico', $criticalStock)
                ->description('Productos bajo mínimo')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color($criticalStock > 0 ? 'warning' : 'success'),
                
            Stat::make('Empleado Destacado', $topEmployee ? $topEmployee->first_name . ' ' . $topEmployee->last_name : 'Sin datos')
                ->description('Mejor vendedor del mes')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('info'),
        ];
    }
}

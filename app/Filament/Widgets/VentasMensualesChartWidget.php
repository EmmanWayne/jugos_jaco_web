<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Bill;

class VentasMensualesChartWidget extends ChartWidget
{
    protected static ?string $heading = '📈 Ventas Mensuales';
    protected static ?string $description = 'Comparativa de ventas de los últimos 12 meses.';

    protected static string $view = 'filament.widgets.ventas-mensuales-chart-widget';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $data[] = Bill::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        }

        $colors = [
            '#f59e42',
            '#fbbf24',
            '#34d399',
            '#60a5fa',
            '#a78bfa',
            '#f472b6',
            '#f87171',
            '#facc15',
            '#4ade80',
            '#38bdf8',
            '#c084fc',
            '#fb7185'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
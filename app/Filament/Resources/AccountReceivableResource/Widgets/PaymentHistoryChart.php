<?php

namespace App\Filament\Resources\AccountReceivableResource\Widgets;

use App\Models\AccountReceivable;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class PaymentHistoryChart extends ChartWidget
{
    protected static ?string $heading = 'Historial de Pagos';

    protected static ?int $sort = 2;

    public ?Model $record = null;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $payments = $this->record->payments()
            ->orderBy('payment_date')
            ->get();

        if ($payments->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => ['Sin pagos'],
            ];
        }

        $labels = [];
        $paymentAmounts = [];
        $cumulativeAmounts = [];
        $balanceAfterPayments = [];

        $cumulativeTotal = 0;
        foreach ($payments as $payment) {
            $labels[] = $payment->payment_date->format('d/m/Y');
            $paymentAmounts[] = (float) $payment->amount;
            $cumulativeTotal += (float) $payment->amount;
            $cumulativeAmounts[] = $cumulativeTotal;
            $balanceAfterPayments[] = (float) $payment->balance_after_payment;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Monto del Pago',
                    'data' => $paymentAmounts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'type' => 'bar',
                ],
                [
                    'label' => 'Total Pagado Acumulado',
                    'data' => $cumulativeAmounts,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'fill' => false,
                ],
                [
                    'label' => 'Saldo Restante',
                    'data' => $balanceAfterPayments,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "L. " + value.toLocaleString(); }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.dataset.label + ": L. " + context.parsed.y.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}

<?php

namespace App\Filament\Actions;

use App\Enums\AccountReceivableStatusEnum;
use App\Models\AccountReceivable;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Support\Enums\MaxWidth;

class MarkAsPaidAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mark_as_paid';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Marcar como Pagado')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->modalWidth(MaxWidth::Medium)
            ->requiresConfirmation()
            ->modalHeading('Marcar cuenta como pagada')
            ->modalDescription('¿Está seguro de que desea marcar esta cuenta por cobrar como pagada completamente?')
            ->form($this->getFormSchema())
            ->action($this->getActionClosure())
            ->visible($this->getVisibilityClosure());
    }

    protected function getFormSchema(): array
    {
        return static::getStaticFormSchema($this);
    }

    public static function getStaticFormSchema($context = null): array
    {
        return [
            Forms\Components\Section::make('Información del Pago Final')
                ->schema([
                    Forms\Components\Placeholder::make('remaining_amount')
                        ->label('Monto Restante')
                        ->content(function () use ($context): string {
                            if ($context && method_exists($context, 'getRecord')) {
                                $record = $context->getRecord();
                                return 'L. ' . number_format($record->remaining_balance, 2);
                            }
                            return 'L. 0.00';
                        }),

                    Forms\Components\Select::make('payment_method')
                        ->label('Método de Pago')
                        ->options([
                            'cash' => 'Efectivo',
                            'deposit' => 'Depósito',
                        ])
                        ->default('cash')
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notas')
                        ->placeholder('Información adicional sobre el pago final')
                        ->maxLength(120)
                        ->rows(3),
                ])
                ->columns(1),
        ];
    }

    protected function getActionClosure(): \Closure
    {
        return function (AccountReceivable $record, array $data): void {
            try {
                // Procesar el pago final usando el servicio
                PaymentService::markAsPaid(
                    $record, 
                    $data['payment_method'] ?? 'cash',
                    $data['notes'] ?? null
                );

                // Enviar notificación
                PaymentService::sendAccountPaidNotification($record);

                // Refrescar datos si es posible
                if (method_exists($this->getLivewire(), 'refreshFormData')) {
                    $this->getLivewire()->refreshFormData([
                        'remaining_balance',
                        'status',
                    ]);
                }

            } catch (\Exception $e) {
                \Filament\Notifications\Notification::make()
                    ->title('Error al marcar como pagado')
                    ->body('Ocurrió un error: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }
        };
    }

    protected function getVisibilityClosure(): \Closure
    {
        return function (AccountReceivable $record): bool {
            return $record->status === AccountReceivableStatusEnum::PENDING;
        };
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? static::getDefaultName());
    }

    public static function table(?string $name = null): \Filament\Tables\Actions\Action
    {
        return \Filament\Tables\Actions\Action::make($name ?? static::getDefaultName())
            ->label('Marcar como Pagado')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->modalWidth(MaxWidth::Medium)
            ->requiresConfirmation()
            ->modalHeading('Marcar cuenta como pagada')
            ->modalDescription('¿Está seguro de que desea marcar esta cuenta por cobrar como pagada completamente?')
            ->form(static::getStaticFormSchema())
            ->action((new static())->getActionClosure())
            ->visible((new static())->getVisibilityClosure());
    }
}

<?php

namespace App\Filament\Actions;

use App\Enums\AccountReceivableStatusEnum;
use App\Models\AccountReceivable;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Support\FilamentNotification;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Facades\Log;

class AddPaymentAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_payment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Agregar Pago')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->modalWidth(MaxWidth::Large)
            ->modalHeading('Registrar Nuevo Pago')
            ->modalDescription('Ingrese los detalles del pago para esta cuenta por cobrar.')
            ->modalSubmitActionLabel('Registrar Pago')
            ->modalCancelActionLabel('Cancelar')
            ->form($this->getFormSchema())
            ->action($this->getActionClosure())
            ->visible($this->getVisibilityClosure());
    }

    protected function getFormSchema(): array
    {
        return static::getFormSchemaForRecord($this->getRecord());
    }

    /**
     * Esquema de formulario unificado - sin información de resumen
     */
    public static function getFormSchemaForRecord(?AccountReceivable $record = null): array
    {
        return [
            Forms\Components\Section::make('Información del Pago')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('amount')
                                ->label('Monto del Pago')
                                ->numeric()
                                ->prefix('L.')
                                ->step(0.01)
                                ->required()
                                ->minValue(0.01)
                                ->rules([
                                    'min:0.01',
                                    function () use ($record) {
                                        return function (string $attribute, $value, \Closure $fail) use ($record) {
                                            if ($record && $value > $record->remaining_balance) {
                                                $fail("El monto no puede ser mayor al saldo pendiente (L. " . number_format($record->remaining_balance, 2) . ").");
                                            }
                                        };
                                    }
                                ])
                                ->maxValue(function () use ($record) {
                                    return $record ? $record->remaining_balance : 999999;
                                })
                                ->helperText(function () use ($record): string {
                                    if ($record) {
                                        return "Saldo pendiente: L. " . number_format($record->remaining_balance, 2);
                                    }
                                    return "Ingrese el monto del pago";
                                })
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ? round((float) $state, 2) : null)
                                ->suffixActions([
                                    Forms\Components\Actions\Action::make('pay_full')
                                        ->label('Total')
                                        ->icon('heroicon-m-currency-dollar')
                                        ->color('primary')
                                        ->tooltip('Pagar el saldo completo')
                                        ->action(function (Forms\Set $set, Forms\Get $get) use ($record) {
                                            if ($record) {
                                                $set('amount', $record->remaining_balance);
                                            }
                                        }),
                                ])
                                ->columnSpan(1),
                            
                            Forms\Components\Select::make('payment_method')
                                ->label('Método de Pago')
                                ->options([
                                    'cash' => 'Efectivo',
                                    'deposit' => 'Depósito',
                                    'transfer' => 'Transferencia',
                                    'check' => 'Cheque',
                                ])
                                ->required()
                                ->default('cash')
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),

                    Forms\Components\DatePicker::make('payment_date')
                        ->label('Fecha de Pago')
                        ->default(now())
                        ->required()
                        ->maxDate(now())
                        ->displayFormat('d/m/Y')
                        ->format('Y-m-d')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notas del Pago')
                        ->maxLength(255)
                        ->rows(2)
                        ->placeholder('Información adicional sobre el pago (opcional)')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getActionClosure(): \Closure
    {
        return static::getPaymentActionClosure();
    }

    /**
     * Closure de acción unificado para procesar pagos
     */
    public static function getPaymentActionClosure(): \Closure
    {
        return function (AccountReceivable $record, array $data): void {
            try {
                // Validar el monto antes de procesar
                $errors = PaymentService::validatePaymentAmount($record, (float) $data['amount']);
                
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        FilamentNotification::error("Error de validación", $error);
                    }
                    return;
                }

                $result = PaymentService::processPayment($record, $data);
                
                if ($result['success']) {
                    $paymentData = $result['data'];
                    $message = "El pago de L. " . number_format($paymentData['payment_amount'], 2) . " ha sido registrado exitosamente.";
                    
                    if ($paymentData['is_fully_paid']) {
                        $message .= " La cuenta ha sido completamente pagada.";
                    } else {
                        $message .= " Saldo restante: L. " . number_format($paymentData['new_balance'], 2);
                    }
                    
                    FilamentNotification::success("Pago registrado", $message);
                } else {
                    FilamentNotification::error("Error al registrar el pago", $result['message']);
                    return;
                }

                // Refrescar componentes relacionados
                static::refreshComponents();

            } catch (\Exception $e) {
                Log::error('Error en AddPaymentAction: ' . $e->getMessage(), [
                    'record_id' => $record->id,
                    'data' => $data,
                    'trace' => $e->getTraceAsString()
                ]);
                FilamentNotification::error("Error al registrar el pago", "Ha ocurrido un error inesperado. Por favor, inténtelo de nuevo.");
            }
        };
    }

    /**
     * Método para refrescar componentes después de un pago exitoso
     */
    protected static function refreshComponents(): void
    {
        try {
            // Intentar obtener el componente Livewire actual
            $livewire = \Livewire\Livewire::current();
            if ($livewire && method_exists($livewire, 'refreshFormData')) {
                $livewire->refreshFormData([
                    'remaining_balance',
                    'status',
                ]);
            }

            // También intentar con el panel de Filament
            $livewire = \Filament\Facades\Filament::getCurrentPanel()?->getCurrentPageLivewire();
            if ($livewire && method_exists($livewire, 'dispatch')) {
                $livewire->dispatch('refreshComponent');
            }
        } catch (\Exception $e) {
            // Silenciar errores de refresh ya que no son críticos
            Log::debug('No se pudo refrescar componentes después del pago: ' . $e->getMessage());
        }
    }

    protected function getVisibilityClosure(): \Closure
    {
        return static::getPaymentVisibilityClosure();
    }

    /**
     * Closure de visibilidad unificado
     */
    public static function getPaymentVisibilityClosure(): \Closure
    {
        return function (AccountReceivable $record): bool {
            return $record->status === AccountReceivableStatusEnum::PENDING && 
                   $record->remaining_balance > 0;
        };
    }

    /**
     * Configuración unificada para todas las variantes de la acción
     */
    protected static function getBaseConfiguration(): array
    {
        return [
            'label' => 'Agregar Pago',
            'icon' => 'heroicon-o-banknotes',
            'color' => 'success',
            'modalWidth' => MaxWidth::Large,
            'modalHeading' => 'Registrar Nuevo Pago',
            'modalDescription' => 'Ingrese los detalles del pago para esta cuenta por cobrar.',
            'modalSubmitActionLabel' => 'Registrar Pago',
            'modalCancelActionLabel' => 'Cancelar',
        ];
    }

    /**
     * Método factory para crear la acción en diferentes contextos
     */
    public static function create(?string $name = null): \Filament\Tables\Actions\Action
    {
        $config = static::getBaseConfiguration();
        
        return \Filament\Tables\Actions\Action::make($name ?? static::getDefaultName())
            ->label($config['label'])
            ->icon($config['icon'])
            ->color($config['color'])
            ->modalWidth($config['modalWidth'])
            ->modalHeading($config['modalHeading'])
            ->modalDescription($config['modalDescription'])
            ->modalSubmitActionLabel($config['modalSubmitActionLabel'])
            ->modalCancelActionLabel($config['modalCancelActionLabel'])
            ->form(function ($record) {
                return static::getFormSchemaForRecord($record);
            })
            ->action(static::getPaymentActionClosure())
            ->visible(static::getPaymentVisibilityClosure())
            ->after(function (): void {
                static::refreshComponents();
            });
    }

    /**
     * Alias para compatibilidad con el código existente
     */
    public static function table(?string $name = null): \Filament\Tables\Actions\Action
    {
        return static::create($name);
    }
}

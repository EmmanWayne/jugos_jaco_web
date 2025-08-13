<?php

namespace App\Filament\Actions;

use App\Enums\AccountReceivableStatusEnum;
use App\Models\AccountReceivable;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Support\Enums\MaxWidth;

class CancelAccountAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'cancel_account';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Cancelar Cuenta')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->modalWidth(MaxWidth::Medium)
            ->requiresConfirmation()
            ->modalHeading('Cancelar cuenta por cobrar')
            ->modalDescription('¿Está seguro de que desea cancelar esta cuenta por cobrar? Esta acción no se puede deshacer.')
            ->form($this->getFormSchema())
            ->action($this->getActionClosure())
            ->visible($this->getVisibilityClosure());
    }

    protected function getFormSchema(): array
    {
        return static::getStaticFormSchema();
    }

    public static function getStaticFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Motivo de Cancelación')
                ->schema([
                    Forms\Components\Textarea::make('cancellation_reason')
                        ->label('Motivo de Cancelación')
                        ->required()
                        ->maxLength(120)
                        ->rows(4)
                        ->placeholder('Describa el motivo por el cual se cancela esta cuenta por cobrar')
                        ->helperText('Este motivo se agregará a las notas de la cuenta.'),

                    Forms\Components\Placeholder::make('warning')
                        ->label('Advertencia')
                        ->content('Esta acción no se puede deshacer. La cuenta será marcada como cancelada y no podrá recibir más pagos.')
                        ->columnSpanFull(),
                ])
        ];
    }

    protected function getActionClosure(): \Closure
    {
        return function (AccountReceivable $record, array $data): void {
            try {
                // Cancelar la cuenta usando el servicio
                PaymentService::cancelAccount($record, $data['cancellation_reason']);

                // Enviar notificación
                PaymentService::sendAccountCancelledNotification();

                // Refrescar datos si es posible
                if (method_exists($this->getLivewire(), 'refreshFormData')) {
                    $this->getLivewire()->refreshFormData([
                        'status',
                        'notes',
                    ]);
                }

            } catch (\Exception $e) {
                \Filament\Notifications\Notification::make()
                    ->title('Error al cancelar cuenta')
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
            ->label('Cancelar Cuenta')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->modalWidth(MaxWidth::Medium)
            ->requiresConfirmation()
            ->modalHeading('Cancelar cuenta por cobrar')
            ->modalDescription('¿Está seguro de que desea cancelar esta cuenta por cobrar? Esta acción no se puede deshacer.')
            ->form(static::getStaticFormSchema())
            ->action((new static())->getActionClosure())
            ->visible((new static())->getVisibilityClosure());
    }
}

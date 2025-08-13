<?php

namespace App\Filament\Resources\AccountReceivableResource\RelationManagers;

use App\Services\PaymentService;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Historial de Pagos';

    protected static ?string $modelLabel = 'Pago';

    protected static ?string $pluralModelLabel = 'Pagos';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto Pagado')
                    ->money('HNL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance_after_payment')
                    ->label('Saldo Después del Pago')
                    ->money('HNL')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'deposit' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Efectivo',
                        'deposit' => 'Depósito',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'cash' => 'Efectivo',
                        'deposit' => 'Depósito',
                    ]),

                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(false)
                    ->after(function (): void {
                        // Recalcular el saldo después de editar un pago usando el servicio
                        PaymentService::recalculateBalances($this->getOwnerRecord());
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(false)
                    ->after(function (): void {
                        // Recalcular el saldo después de eliminar un pago usando el servicio
                        PaymentService::recalculateBalances($this->getOwnerRecord());

                        Notification::make()
                            ->title('Pago eliminado y saldo recalculado')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (): void {
                            // Recalcular después de eliminar pagos en masa usando el servicio
                            PaymentService::recalculateBalances($this->getOwnerRecord());
                        }),
                ]),
            ])
            ->defaultSort('payment_date', 'desc')
            ->emptyStateHeading('Sin pagos registrados')
            ->emptyStateDescription('Aún no se han registrado pagos para esta cuenta por cobrar.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}

<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Client;
use App\Models\Employee;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EmployeeClientsTableWidget extends BaseWidget
{
    public ?Employee $record = null;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Clientes Asignados';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()->where('employee_id', $this->record?->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->getStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('business_name')
                    ->label('Nombre del Negocio')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Sin nombre comercial'),
                    
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Teléfono copiado')
                    ->copyMessageDuration(1500),
                    
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('Departamento')
                    ->options(\App\Enums\DepartmentEnum::class),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Client $record): string => \App\Filament\Resources\ClientResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Sin clientes asignados')
            ->emptyStateDescription('Este empleado no tiene clientes asignados actualmente.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}

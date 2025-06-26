<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignedProductResource\Pages;
use App\Filament\Resources\AssignedProductResource\RelationManagers;
use App\Filament\Support\FilamentNotification;
use App\Models\AssignedProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssignedProductResource extends Resource
{
    protected static ?string $model = AssignedProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ventas';

    protected static ?string $navigationLabel = 'Asignación de Productos';

    protected static ?string $modelLabel = 'Asignación de Productos';

    protected static ?string $pluralModelLabel = 'Asignaciones de Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Empleado')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name),
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Fecha')
                    ->default(now()->format('Y-m-d'))
                    ->minDate(now()->format('Y-m-d'))
                    ->disabled(fn($livewire) => $livewire instanceof Pages\EditAssignedProduct)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->searchable()
                    ->label('Empleado')
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->join('employees', 'assigned_products.employee_id', '=', 'employees.id')
                            ->orderBy('employees.first_name', $direction)
                            ->orderBy('employees.last_name', $direction)
                            ->select('assigned_products.*');
                    }),
                Tables\Columns\TextColumn::make('date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Fecha de asignación'),
                Tables\Columns\TextColumn::make('details_count')
                    ->counts('details')
                    ->label('Cantidad de productos'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Creado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->label('Empleado')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn($query) => $query->whereDate('date', '>=', $data['date_from']),
                            )
                            ->when(
                                $data['date_until'],
                                fn($query) => $query->whereDate('date', '<=', $data['date_until']),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::disabledForPastOrFutureDates($record)),
                Tables\Actions\DeleteAction::make()
                    ->using(function ($record, $action) {
                        if ($record->details->count() > 0) {
                            FilamentNotification::warning(
                                title: 'Asignación de productos',
                                body: 'No se puede eliminar la asignación de productos debido a que tiene productos asignados.',
                            );

                            $action->cancel();
                        }

                        $record->delete();
                    })
                    ->visible(fn($record) => static::disabledForPastOrFutureDates($record))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function ($records, $action) {
                            foreach ($records as $record) {
                                if ($record->details->count() > 0) {
                                    FilamentNotification::warning(
                                        title: 'Asignación de productos',
                                        body: "No se puede eliminar la asignación de productos de {$record->full_name} debido a que tiene productos asignados.",
                                    );

                                    $action->cancel();
                                    return;
                                }
                            }

                            AssignedProduct::destroy($records->pluck('id')->toArray());
                        })
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignedProducts::route('/'),
            'create' => Pages\CreateAssignedProduct::route('/create'),
            'view' => Pages\ViewAssignedProduct::route('/{record}'),
            'edit' => Pages\EditAssignedProduct::route('/{record}/edit'),
        ];
    }

    private static function disabledForPastOrFutureDates($record): bool
    {
        return $record->date->format('Y-m-d') === now()->format('Y-m-d');
    }
}

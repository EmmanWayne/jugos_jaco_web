<?php

namespace App\Filament\Resources;

use App\Enums\ReconciliationStatusEnum;
use App\Filament\Resources\DailySalesReconciliationResource\Pages;
use App\Models\DailySalesReconciliation;
use App\Models\Employee;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DailySalesReconciliationResource extends Resource
{
    protected static ?string $model = DailySalesReconciliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Finanzas';

    protected static ?string $navigationLabel = 'Reconciliaciones Diarias';

    protected static ?string $label = 'Reconciliación Diaria';

    protected static ?string $pluralLabel = 'Reconciliaciones Diarias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Información básica de la reconciliación diaria')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('branch_id')
                                    ->label('Sucursal')
                                    ->relationship('branch', 'name')
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('employee_id')
                                    ->label('Empleado')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->required(),

                                Forms\Components\DatePicker::make('reconciliation_date')
                                    ->label('Fecha de Reconciliación')
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),

                Forms\Components\Section::make('Ventas del Día')
                    ->description('Resumen de las ventas realizadas')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_cash_sales')
                                    ->label('Ventas de Contado')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->required(),

                                Forms\Components\TextInput::make('total_credit_sales')
                                    ->label('Ventas de Crédito')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->required(),

                                Forms\Components\TextInput::make('total_sales')
                                    ->label('Total de Ventas')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Movimientos de Efectivo')
                    ->description('Efectivo recibido, depósitos y cobros')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_cash_received')
                                    ->label('Efectivo Recibido')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->required(),

                                Forms\Components\TextInput::make('total_deposits')
                                    ->label('Depósitos')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->default(0),

                                Forms\Components\TextInput::make('total_collections')
                                    ->label('Cobros/Pagos')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->default(0),
                            ]),
                    ]),

                Forms\Components\Section::make('Reconciliación')
                    ->description('Cálculos y diferencias')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('total_cash_expected')
                                    ->label('Efectivo Esperado')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('cash_difference')
                                    ->label('Diferencia')
                                    ->numeric()
                                    ->prefix('L')
                                    ->step(0.01)
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\Select::make('status')
                                    ->label('Estado')
                                    ->options([
                                        ReconciliationStatusEnum::PENDING->value => ReconciliationStatusEnum::PENDING->getLabel(),
                                        ReconciliationStatusEnum::COMPLETED->value => ReconciliationStatusEnum::COMPLETED->getLabel(),
                                        ReconciliationStatusEnum::WITH_DIFFERENCES->value => ReconciliationStatusEnum::WITH_DIFFERENCES->getLabel(),
                                    ])
                                    ->default(ReconciliationStatusEnum::PENDING)
                                    ->required(),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->placeholder('Observaciones adicionales...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reconciliation_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Empleado')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_cash_sales')
                    ->label('Contado')
                    ->money('HNL')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_credit_sales')
                    ->label('Crédito')
                    ->money('HNL')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('warning'),

                Tables\Columns\TextColumn::make('total_cash_received')
                    ->label('Efectivo')
                    ->money('HNL')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_deposits')
                    ->label('Depósitos')
                    ->money('HNL')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_collections')
                    ->label('Pagos')
                    ->money('HNL')
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('cash_difference')
                    ->label('Diferencia')
                    ->money('HNL')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => $state->getColor())
                    ->icon(fn ($state) => $state->getIcon()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('branch', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employee', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        ReconciliationStatusEnum::PENDING->value => ReconciliationStatusEnum::PENDING->getLabel(),
                        ReconciliationStatusEnum::COMPLETED->value => ReconciliationStatusEnum::COMPLETED->getLabel(),
                        ReconciliationStatusEnum::WITH_DIFFERENCES->value => ReconciliationStatusEnum::WITH_DIFFERENCES->getLabel(),
                    ]),

                Tables\Filters\Filter::make('reconciliation_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('reconciliation_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('reconciliation_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('reconciliation_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailySalesReconciliations::route('/'),
            'create' => Pages\CreateDailySalesReconciliation::route('/create'),
            'view' => Pages\ViewDailySalesReconciliation::route('/{record}'),
            'edit' => Pages\EditDailySalesReconciliation::route('/{record}/edit'),
        ];
    }
}
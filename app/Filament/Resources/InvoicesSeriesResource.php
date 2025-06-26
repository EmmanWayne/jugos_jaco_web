<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoicesSeriesResource\Pages;
use App\Filament\Support\FilamentNotification;
use App\Models\InvoicesSeries;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoicesSeriesResource extends Resource
{
    protected static ?string $model = InvoicesSeries::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la serie de facturación')
                    ->description('Datos generales de la serie de facturas')
                    ->schema([
                        Forms\Components\TextInput::make('cai')
                            ->label('CAI')
                            ->helperText('Código de Autorización de Impresión')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignorable: fn(?InvoicesSeries $record) => $record),

                        Forms\Components\Select::make('branch_id')
                            ->label('Sucursal')
                            ->relationship('branch', 'name')
                            ->required(),

                        Forms\Components\Grid::make('')
                            ->schema([
                                Forms\Components\TextInput::make('initial_range')
                                    ->label('Rango Inicial')
                                    ->helperText('Número inicial del rango de facturación')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(20),

                                Forms\Components\TextInput::make('end_range')
                                    ->label('Rango Final')
                                    ->helperText('Número final del rango de facturación')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(20),
                                Forms\Components\DatePicker::make('expiration_date')
                                    ->label('Fecha límite de emisión')
                                    ->helperText('Fecha límite de emisión')
                                    ->required(),
                                Forms\Components\TextInput::make('prefix')
                                    ->label('Prefijo')
                                    ->helperText('Prefijo para el número de factura')
                                    ->required()
                                    ->placeholder('001-002-01')
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('mask_format')
                                    ->label('Formato de Máscara')
                                    ->helperText('El formato de máscara es la cantidad de digitos que tendra el número de factura sin el prefijo')
                                    ->required()
                                    ->default('00000000')
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('current_number')
                                    ->label('Factura Actual')
                                    ->helperText('Número actual de la factura')
                                    ->required()
                                    ->numeric()
                                    ->visible(fn(string $context) => in_array($context, ['edit', 'view', 'create'])),
                            ])
                            ->columns(3),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->helperText('Estado de la factura')
                            ->required()
                            ->options([
                                'Activa' => 'Activa',
                                'Expirada' => 'Expirada',
                                'Completada' => 'Completada',
                            ])
                            ->default('Activa'),
                    ])
                    ->columns(2),
            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Sucursal'),

                Tables\Columns\TextColumn::make('initial_range')
                    ->label('Rango Inicial')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_range')
                    ->label('Rango Final')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Fecha límite de emisión')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->icon(fn($state) => match ($state) {
                        'Activa' => 'heroicon-o-check-circle',
                        'Expirada' => 'heroicon-o-x-circle',
                        'Completada' => 'heroicon-o-check-badge',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn($state) => match ($state) {
                        'Activa' => 'success',
                        'Expirada' => 'danger',
                        'Completada' => 'primary',
                        default => 'gray',
                    })
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('prefix')
                    ->label('Prefijo')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_number')
                    ->label('Factura Actual')
                    ->sortable()
                    ->searchable(),
            ])->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->before(function ($action, $record) {
                        if ($record->status === 'Activa') {
                            FilamentNotification::error(
                                title: 'Serie de facturación activa',
                                body: 'La serie de facturación está activa y no se puede eliminar.'
                            );

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($action, $records) {
                            $activeSeriesCount = $records->where('status', 'Activa')->count();

                            if ($activeSeriesCount > 0) {
                                FilamentNotification::error(
                                    title: 'Series de facturación activas',
                                    body: "No se pueden eliminar {$activeSeriesCount} series activas. Cambie su estado antes de eliminarlas."
                                );

                                $action->cancel();
                            }

                            $records->filter(fn($record) => $record->status !== 'Activa')
                                ->each(fn($record) => $record->delete());
                        }),
                ]),
            ]);
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
            'index' => Pages\ListInvoicesSeries::route('/'),
            'create' => Pages\CreateInvoicesSeries::route('/create'),
            'view' => Pages\ViewInvoicesSeries::route('/{record}'),
            'edit' => Pages\EditInvoicesSeries::route('/{record}/edit'),
        ];
    }


    public static function getNavigationGroup(): ?string
    {
        return 'Facturación';
    }
    public static function getNavigationLabel(): string
    {
        return 'Facturas';
    }
    public static function getModelLabel(): string
    {
        return 'Serie de Factura';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Series de Facturas';
    }
    public static function getNavigationSort(): ?int
    {
        return 2; // Adjust the sort order as needed
    }
}

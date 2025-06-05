<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoicesSeriesResource\Pages;
use App\Filament\Resources\InvoicesSeriesResource\RelationManagers;
use App\Models\InvoicesSeries;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesSeriesResource extends Resource
{
    protected static ?string $model = InvoicesSeries::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('cai')
                    ->label('CAI')
                    ->helperText('Código de Autorización de Impresión')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignorable: fn(?InvoicesSeries $record) => $record),

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
                    ->label('Fecha de Expiración')
                    ->helperText('Fecha limite de emisión')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->helperText('Estado de la serie de facturación')
                    ->required()
                    ->options([
                        'Activa' => 'Activa',
                        'Expirada' => 'Expirada',
                        'Completada' => 'Completada',
                    ])
                    ->default('Activada'),

                Forms\Components\TextInput::make('mask_format')
                    ->label('Formato de Máscara')
                    ->helperText('Formato de máscara para el número de factura')
                    ->required()
                    ->default('00000000')
                    ->maxLength(20),

                Forms\Components\TextInput::make('prefix')
                    ->label('Prefijo')
                    ->helperText('Prefijo para el número de factura')
                    ->required()
                    ->default('000-000-00')
                    ->maxLength(20),

                Forms\Components\TextInput::make('current_number')
                    ->label('Número Actual')
                    ->helperText('Número actual de la serie de facturación')
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->visible(fn(string $context) => in_array($context, ['edit', 'view', 'create']))
                    ->disabled(fn(string $context) => $context === 'edit'),


                Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
            ])->columns(2)
            ->columns([
                'sm' => 1,
                'md' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cai')
                    ->label('CAI')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('initial_range')
                    ->label('Rango Inicial')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_range')
                    ->label('Rango Final')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Fecha de Expiración')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mask_format')
                    ->label('Formato de Máscara')
                    ->sortable(),

                Tables\Columns\TextColumn::make('prefix')
                    ->label('Prefijo')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_number')
                    ->label('Número Actual')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Sucursal')
                    ->sortable(),
            ])->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

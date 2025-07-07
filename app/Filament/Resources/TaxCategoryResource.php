<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxCategoryResource\Pages;
use App\Models\TaxCategory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxCategoryResource extends Resource
{
    protected static ?string $model = TaxCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Configuración';
    
    protected static ?string $navigationLabel = 'Categorías de Impuestos';
    
    protected static ?string $modelLabel = 'Categoría de Impuesto';
    
    protected static ?string $pluralModelLabel = 'Categorías de Impuestos';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la Categoría de Impuesto')
                    ->description('Configure las categorías de impuestos para la facturación')
                    ->schema([
                        Section::make('')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(64)
                                    ->placeholder('Ej: ISV 15%, Exento'),
                                TextInput::make('rate')
                                    ->label('Tasa (%)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->placeholder('Ej: 15.00'),
                            ]),

                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Select::make('type_tax_use')
                                    ->label('Tipo de uso')
                                    ->required()
                                    ->options([
                                        'sale' => 'Ventas',
                                        'purchase' => 'Compras',
                                        'all' => 'Ambos',
                                    ])
                                    ->default('sale'),
                                TextInput::make('sequence_invoice')
                                    ->label('Orden en factura')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1),
                            ]),

                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Select::make('display_type')
                                    ->label('Tipo de visualización')
                                    ->required()
                                    ->options([
                                        'tax_source' => 'Para Productos (Fuente)',
                                        'base_display' => 'Base Imponible (Factura)',
                                        'tax_display' => 'Impuesto Calculado (Factura)',
                                    ])
                                    ->default('tax_source')
                                    ->helperText('Define cómo se usa esta categoría')
                                    ->live(),
                                Select::make('calculation_type')
                                    ->label('Tipo de cálculo')
                                    ->required()
                                    ->options([
                                        'tax' => 'Impuesto',
                                        'base' => 'Base',
                                        'exempt' => 'Exento',
                                    ])
                                    ->default('tax'),
                            ]),

                        Section::make('')
                            ->columns(2)
                            ->schema([
                                Select::make('base_tax_id')
                                    ->label('Categoría base')
                                    ->placeholder('Seleccione una categoría base (opcional)')
                                    ->options(fn() => TaxCategory::forProducts()->active()->pluck('name', 'id'))
                                    ->helperText('Solo para líneas de display en factura')
                                    ->visible(fn(Get $get) => in_array($get('display_type'), ['base_display', 'tax_display'])),
                                Forms\Components\Toggle::make('is_for_products')
                                    ->label('Para productos')
                                    ->default(true)
                                    ->helperText('Si está activo, se puede asignar a productos')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if (!$state) {
                                            $set('display_type', 'base_display');
                                        } else {
                                            $set('display_type', 'tax_source');
                                        }
                                    }),
                            ]),

                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción')
                                    ->maxLength(500)
                                    ->placeholder('Descripción opcional del impuesto'),
                            ]),

                        Section::make('')
                            ->columns(1)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Estado activo')
                                    ->default(true)
                                    ->required()
                                    ->helperText('Desactive para ocultar esta categoría'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->label('Tasa')
                    ->formatStateUsing(fn($state) => "{$state}%")
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn($state) => match($state) {
                        'tax_source' => 'Para Productos',
                        'base_display' => 'Base Imponible',
                        'tax_display' => 'Impuesto Calculado',
                        default => $state
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'tax_source' => 'success',
                        'base_display' => 'info',
                        'tax_display' => 'warning',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('calculation_type')
                    ->label('Cálculo')
                    ->formatStateUsing(fn($state) => match($state) {
                        'tax' => 'Impuesto',
                        'base' => 'Base',
                        'exempt' => 'Exento',
                        default => $state
                    })
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'tax' => 'primary',
                        'base' => 'info',
                        'exempt' => 'gray',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('baseTaxCategory.name')
                    ->label('Categoría Base')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('sequence_invoice')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_for_products')
                    ->label('Para Productos')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('display_type')
                    ->label('Tipo de Display')
                    ->options([
                        'tax_source' => 'Para Productos',
                        'base_display' => 'Base Imponible',
                        'tax_display' => 'Impuesto Calculado',
                    ]),
                Tables\Filters\SelectFilter::make('calculation_type')
                    ->label('Tipo de Cálculo')
                    ->options([
                        'tax' => 'Impuesto',
                        'base' => 'Base',
                        'exempt' => 'Exento',
                    ]),
                Tables\Filters\TernaryFilter::make('is_for_products')
                    ->label('Para Productos')
                    ->boolean()
                    ->trueLabel('Sí')
                    ->falseLabel('No'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Activo')
                    ->falseLabel('Inactivo'),
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
            ->defaultSort('sequence_invoice', 'asc')
            ->groups([
                Tables\Grouping\Group::make('display_type')
                    ->label('Tipo de Display')
                    ->titlePrefixedWithLabel(false),
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
            'index' => Pages\ListTaxCategories::route('/'),
            'create' => Pages\CreateTaxCategory::route('/create'),
            'view' => Pages\ViewTaxCategory::route('/{record}'),
            'edit' => Pages\EditTaxCategory::route('/{record}/edit'),
        ];
    }
}

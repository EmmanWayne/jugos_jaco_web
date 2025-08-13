<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductUnitResource\Pages;
use App\Models\ProductUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductUnitResource extends Resource
{
    protected static ?string $model = ProductUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Unidades de Producto';

    protected static ?string $modelLabel = 'Unidad de Producto';

    protected static ?string $pluralModelLabel = 'Unidades de Producto';

    protected static ?string $navigationGroup = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Principal')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Producto')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('unit_id')
                            ->label('Unidad de Medida')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('conversion_factor')
                            ->label('Factor de Conversión')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->helperText('Factor para convertir a la unidad base (ej: 1 caja = 24 unidades)')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Toggle::make('is_base_unit')
                            ->label('Unidad Base')
                            ->helperText('Si es la unidad base para control de inventario')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_sellable')
                            ->label('Vendible')
                            ->helperText('Si se puede vender en esta unidad')
                            ->default(true)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_purchasable')
                            ->label('Comprable')
                            ->helperText('Si se puede comprar en esta unidad')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->helperText('Si la unidad está activa')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unidad')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('conversion_factor')
                    ->label('Factor Conversión')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_base_unit')
                    ->label('Unidad Base')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unidad')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_base_unit')
                    ->label('Unidad Base'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ])
            ->defaultSort('product.name');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información Principal')
                    ->schema([
                        Infolists\Components\TextEntry::make('product.name')
                            ->label('Producto')
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('unit.name')
                            ->label('Unidad de Medida')
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('conversion_factor')
                            ->label('Factor de Conversión')
                            ->suffix(' unidades base'),

                        Infolists\Components\TextEntry::make('unit.abbreviation')
                            ->label('Abreviación de la Unidad'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Configuración')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_base_unit')
                            ->label('Unidad Base')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        Infolists\Components\IconEntry::make('is_sellable')
                            ->label('Vendible')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        Infolists\Components\IconEntry::make('is_purchasable')
                            ->label('Comprable')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Activo')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Información de Auditoría')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y h:i:s a')
                            ->icon('heroicon-o-calendar-days'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y h:i:s a')
                            ->icon('heroicon-o-clock')
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsible(),
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
            'index' => Pages\ListProductUnits::route('/'),
            'create' => Pages\CreateProductUnit::route('/create'),
            'view' => Pages\ViewProductUnit::route('/{record}'),
            'edit' => Pages\EditProductUnit::route('/{record}/edit'),
        ];
    }
}

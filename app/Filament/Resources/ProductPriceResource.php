<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductPriceResource\Pages;
use App\Models\ProductPrice;
use App\Models\ProductUnit;
use App\Models\TaxCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductPriceResource extends Resource
{
    protected static ?string $model = ProductPrice::class;

    protected static ?string $navigationGroup = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Precio')
                    ->description('Configure el precio del producto con sus respectivos impuestos')
                    ->schema([
                        Forms\Components\Section::make('')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Producto')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('product_unit_id', null);
                                    }),

                                Forms\Components\Select::make('product_unit_id')
                                    ->label('Unidad de medida')
                                    ->options(function (callable $get) {
                                        $productId = $get('product_id');
                                        if (!$productId) {
                                            return [];
                                        }
                                        
                                        return ProductUnit::where('product_id', $productId)
                                            ->active()
                                            ->with('unit')
                                            ->get()
                                            ->mapWithKeys(function ($productUnit) {
                                                return [$productUnit->id => $productUnit->unit->name . ' (' . $productUnit->conversion_factor . ')'];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->helperText('Seleccione la unidad de medida para este precio'),
                            ]),

                        Forms\Components\Section::make('')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('type_price_id')
                                    ->relationship('typePrice', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Tipo de precio'),

                                Forms\Components\TextInput::make('price')
                                    ->label('Precio')
                                    ->required()
                                    ->numeric()
                                    ->prefix('L.')
                                    ->maxValue(99999.99)
                                    ->minValue(0)
                                    ->step(0.01),
                            ]),

                        Forms\Components\Section::make('')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Toggle::make('price_include_tax')
                                    ->label('Precio incluye impuesto')
                                    ->default(false)
                                    ->helperText('Marque si el precio ya incluye el impuesto')
                                    ->inline(false),

                                Forms\Components\Select::make('tax_category_id')
                                    ->label('Categoría de impuesto')
                                    ->options(TaxCategory::getForProductSelection())
                                    ->placeholder('Seleccione una categoría de impuesto')
                                    ->helperText('Seleccione la categoría fiscal que aplica a este precio')
                                    ->searchable(),
                            ]),
                    ]),
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

                Tables\Columns\TextColumn::make('productUnit.unit.name')
                    ->label('Unidad')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($record) {
                        if (!$record->productUnit) {
                            return 'Sin unidad';
                        }
                        return $record->productUnit->unit->name . ' (' . $record->productUnit->conversion_factor . ')';
                    }),

                Tables\Columns\TextColumn::make('typePrice.name')
                    ->label('Tipo de precio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn($record) => "L. " . number_format($record->price, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('taxCategory.name')
                    ->label('Categoría de Impuesto')
                    ->placeholder('Sin impuesto')
                    ->badge()
                    ->color(fn($record) => $record->taxCategory ? 'success' : 'gray'),

                Tables\Columns\IconColumn::make('price_include_tax')
                    ->label('Incluye Impuesto')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('calculated_info')
                    ->label('Precio Calculado')
                    ->state(function ($record) {
                        if (!$record->taxCategory) {
                            return 'Sin impuesto';
                        }

                        return 'L. ' . number_format($record->getPriceWithTax(), 2);
                    })
                    ->tooltip(function ($record) {
                        if (!$record->taxCategory) {
                            return 'Este precio no tiene impuesto asociado';
                        }

                        $basePrice = $record->getPriceWithoutTax();
                        $taxAmount = $record->getTaxAmount();
                        $priceWithTax = $record->getPriceWithTax();
                        $taxName = $record->taxCategory->name;

                        return "💰 Precio base: L. " . number_format($basePrice, 2) . "\n" .
                               "📈 {$taxName}: L. " . number_format($taxAmount, 2) . "\n";
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
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

                Tables\Filters\SelectFilter::make('product_unit_id')
                    ->label('Unidad de medida')
                    ->relationship('productUnit.unit', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('tax_category_id')
                    ->label('Categoría de Impuesto')
                    ->relationship('taxCategory', 'name')
                    ->placeholder('Todas las categorías'),

                Tables\Filters\TernaryFilter::make('price_include_tax')
                    ->label('Precio incluye impuesto')
                    ->boolean()
                    ->trueLabel('Incluye impuesto')
                    ->falseLabel('No incluye impuesto')
                    ->placeholder('Todos'),

                Tables\Filters\Filter::make('with_tax')
                    ->label('Solo precios con impuesto')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('tax_category_id')),

                Tables\Filters\Filter::make('without_tax')
                    ->label('Solo precios sin impuesto')
                    ->query(fn(Builder $query): Builder => $query->whereNull('tax_category_id')),
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
            'index' => Pages\ListProductPrices::route('/'),
            'create' => Pages\CreateProductPrice::route('/create'),
            'view' => Pages\ViewProductPrice::route('/{record}'),
            'edit' => Pages\EditProductPrice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Precios De Productos';
    }

    public static function getModelLabel(): string
    {
        return 'Precio de producto';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Precios de productos';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationSort(): int
    {
        return 4;
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductPriceResource\Pages;
use App\Filament\Resources\ProductPriceResource\RelationManagers;
use App\Models\ProductPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductPriceResource extends Resource
{
    protected static ?string $model = ProductPrice::class;

    protected static ?string $navigationGroup = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Producto'),

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
                    ->minValue(0),
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

                Tables\Columns\TextColumn::make('typePrice.name')
                    ->label('Tipo de precio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->formatStateUsing(fn($record) => "L. {$record->price}")
                    ->searchable()
                    ->sortable(),

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

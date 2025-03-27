<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypePriceResource\Pages;
use App\Filament\Resources\TypePriceResource\RelationManagers;
use App\Models\TypePrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypePriceResource extends Resource
{
    protected static ?string $model = TypePrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Productos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
            'index' => Pages\ListTypePrices::route('/'),
            'create' => Pages\CreateTypePrice::route('/create'),
            'view' => Pages\ViewTypePrice::route('/{record}'),
            'edit' => Pages\EditTypePrice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Tipos de precio';
    }

    public static function getModelLabel(): string
    {
        return 'Tipo de precio';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tipos de precio';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }
}

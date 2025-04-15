<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RawMaterialsInventoryResource\Pages;
use App\Filament\Resources\RawMaterialsInventoryResource\RelationManagers;
use App\Models\RawMaterialsInventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RawMaterialsInventoryResource extends Resource
{
    protected static ?string $model = RawMaterialsInventory::class;

    protected static ?string $navigationGroup = 'Inventarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('unit')
                    ->label('Unidad')
                    ->options([
                        'u' => 'Unidad',
                        'kg' => 'Kilogramo',
                        'g' => 'Gramo',
                        'l' => 'Litro',
                        'ml' => 'Mililitro',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('minimum_stock')
                    ->label('Stock Mínimo')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(65535),
                Forms\Components\Select::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
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
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->formatStateUsing(fn($record) => "{$record->quantity} {$record->unit}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_stock')
                    ->label('Stock Mínimo'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Sucursal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListRawMaterialsInventories::route('/'),
            'create' => Pages\CreateRawMaterialsInventory::route('/create'),
            'view' => Pages\ViewRawMaterialsInventory::route('/{record}'),
            'edit' => Pages\EditRawMaterialsInventory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Materia Prima';
    }

    public static function getModelLabel(): string
    {
        return 'Materia Prima';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Materias Primas';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-beaker';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }
}

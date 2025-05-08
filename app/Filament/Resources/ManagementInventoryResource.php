<?php

namespace App\Filament\Resources;

use App\Enums\TypeInventoryManagementEnum;
use App\Filament\Resources\ManagementInventoryResource\Pages;
use App\Models\ManagementInventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ManagementInventoryResource extends Resource
{
    protected static ?string $model = ManagementInventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Inventario';
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state) => TypeInventoryManagementEnum::from($state)->getLabel())
                    ->badge()
                    ->color(fn(string $state) => TypeInventoryManagementEnum::getColor($state))
                    ->extraAttributes([
                        'class' => 'text-base font-medium',
                    ])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(100)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Creado por')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListManagementInventories::route('/'),
            'view' => Pages\ViewManagementInventory::route('/{record}'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Mov. de Inventario';
    }

    public static function getModelLabel(): string
    {
        return 'Mov. de Inventario';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mov. de Inventario';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Inventario';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}

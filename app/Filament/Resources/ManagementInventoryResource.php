<?php

namespace App\Filament\Resources;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipo de Movimiento')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'dañado' => 'Dañado',
                        'devolución' => 'Devolución',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Hidden::make('model_type')
                    ->default('App\Models\RawMaterialsInventory'),
                Forms\Components\Select::make('model_id')
                    ->label('Materia Prima')
                    ->options(
                        fn() => \App\Models\RawMaterialsInventory::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => Auth::user()->name),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'dañado' => 'Dañado',
                        'devolución' => 'Devolución',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'entrada' => 'success',
                        'salida' => 'warning',
                        'dañado' => 'danger',
                        'devolución' => 'info',
                        default => 'gray',
                    })
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
            'create' => Pages\CreateManagementInventory::route('/create'),
            'edit' => Pages\EditManagementInventory::route('/{record}/edit'),
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

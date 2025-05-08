<?php

namespace App\Filament\Resources\ManagementInventoryResource\RelationManagers;

use App\Enums\TypeInventoryManagementEnum;
use App\Models\ManagementInventory;
use App\Services\ManagementInventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MovementsInventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';
    
    protected static ?string $title = 'Movimientos de inventario';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options(TypeInventoryManagementEnum::getOptions())
                    ->label('Tipo')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('Cantidad')
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->label('Descripción')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => TypeInventoryManagementEnum::getColor($state)),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar movimiento')
                    ->using(function (array $data, RelationManager $livewire): ManagementInventory {
                        $inventoryService = app(ManagementInventoryService::class);
                        
                        return $inventoryService->processMovement(
                            model: $livewire->getOwnerRecord(), 
                            quantity: (float)$data['quantity'],    
                            type: $data['type'],               
                            description: $data['description'],      
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detalle del movimiento'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
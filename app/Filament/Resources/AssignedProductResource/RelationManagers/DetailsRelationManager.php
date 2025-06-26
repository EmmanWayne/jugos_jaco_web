<?php

namespace App\Filament\Resources\AssignedProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $recordTitleAttribute = 'product.name';

    protected static ?string $modelLabel = 'Producto Asignado';

    protected static ?string $title = 'Productos Asignados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Producto'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->label('Cantidad'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto'),
                Tables\Columns\TextColumn::make('product.content_type')
                    ->label('Tipo de Contenido'),
                Tables\Columns\TextColumn::make('product.content')
                    ->label('Contenido'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad asignada'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Productos Asignados')
                    ->visible(fn() => $this->disabledForPastOrFutureDates())
                    ->modalHeading('Asignar producto al empleado'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn() => $this->disabledForPastOrFutureDates()),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn() => $this->disabledForPastOrFutureDates()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => $this->disabledForPastOrFutureDates()),
                ]),
            ]);
    }

    private function disabledForPastOrFutureDates(): bool
    {
        $assignedProduct = $this->getOwnerRecord();
        return $assignedProduct->date->format('Y-m-d') === now()->format('Y-m-d');
    }
}

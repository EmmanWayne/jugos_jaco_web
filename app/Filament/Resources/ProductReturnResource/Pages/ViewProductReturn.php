<?php

namespace App\Filament\Resources\ProductReturnResource\Pages;

use App\Filament\Resources\ProductReturnResource;
use App\Services\ProductReturnService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;

class ViewProductReturn extends ViewRecord
{
    protected static string $resource = ProductReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->before(function (Model $record) {
                    $this->reverseInventoryMovement($record);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información de la Devolución')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('product.name')
                            ->label('Producto'),
                        TextEntry::make('employee.full_name')
                            ->label('Empleado'),
                        TextEntry::make('type')
                            ->label('Tipo')
                            ->badge()
                            ->color(fn($state) => $state->getColor())
                            ->formatStateUsing(fn($state) => $state->getLabel()),
                        TextEntry::make('quantity')
                            ->label('Cantidad')
                            ->numeric(2),
                        TextEntry::make('reason')
                            ->label('Motivo'),
                        TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Fecha de Registro')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Revierte el movimiento de inventario al eliminar una devolución
     */
    private function reverseInventoryMovement(Model $productReturn): void
    {
        $productReturnService = new ProductReturnService();
        $productReturnService->reverseInventoryMovement($productReturn);
    }
}
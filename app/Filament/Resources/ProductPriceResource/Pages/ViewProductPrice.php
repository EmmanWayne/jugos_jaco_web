<?php

namespace App\Filament\Resources\ProductPriceResource\Pages;

use App\Filament\Resources\ProductPriceResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewProductPrice extends ViewRecord
{
    protected static string $resource = ProductPriceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Producto')
                    ->description('Datos básicos del precio configurado')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('product.name')
                            ->label('Producto')
                            ->weight('bold'),

                        TextEntry::make('productUnit.unit.name')
                            ->label('Unidad de medida')
                            ->formatStateUsing(function ($record) {
                                if (!$record->productUnit) {
                                    return 'Sin unidad definida';
                                }
                                return $record->productUnit->unit->name . ' (Factor: ' . $record->productUnit->conversion_factor . ')';
                            })
                            ->badge()
                            ->color('info'),

                        TextEntry::make('typePrice.name')
                            ->label('Tipo de precio')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('price')
                            ->label('Precio')
                            ->formatStateUsing(fn($state) => 'L. ' . number_format($state, 2))
                            ->weight('bold')
                            ->size('lg'),
                    ]),

                Section::make('Información Fiscal')
                    ->description('Configuración de impuestos y cálculos')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('taxCategory.name')
                            ->label('Categoría de impuesto')
                            ->placeholder('Sin impuesto configurado')
                            ->badge()
                            ->color(fn($record) => $record->taxCategory ? 'success' : 'gray'),

                        TextEntry::make('price_include_tax')
                            ->label('Precio incluye impuesto')
                            ->formatStateUsing(fn($state) => $state ? 'Sí' : 'No')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'warning'),

                        TextEntry::make('price_without_tax')
                            ->label('Precio sin impuesto')
                            ->getStateUsing(function ($record) {
                                return 'L. ' . number_format($record->getPriceWithoutTax(), 2);
                            })
                            ->visible(fn($record) => $record->taxCategory),

                        TextEntry::make('tax_amount')
                            ->label('Monto del impuesto')
                            ->getStateUsing(function ($record) {
                                return 'L. ' . number_format($record->getTaxAmount(), 2);
                            })
                            ->visible(fn($record) => $record->taxCategory),

                        TextEntry::make('price_with_tax')
                            ->label('Precio con impuesto')
                            ->getStateUsing(function ($record) {
                                return 'L. ' . number_format($record->getPriceWithTax(), 2);
                            })
                            ->weight('bold')
                            ->size('lg')
                            ->visible(fn($record) => $record->taxCategory),
                    ]),

                Section::make('Información de Registro')
                    ->description('Fechas de creación y actualización')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y h:i:s a')
                            ->icon('heroicon-o-calendar-days'),

                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y h:i:s a')
                            ->icon('heroicon-o-clock')
                            ->since(),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

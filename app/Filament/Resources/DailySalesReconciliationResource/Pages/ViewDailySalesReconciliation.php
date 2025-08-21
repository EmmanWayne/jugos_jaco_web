<?php

namespace App\Filament\Resources\DailySalesReconciliationResource\Pages;

use App\Filament\Resources\DailySalesReconciliationResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewDailySalesReconciliation extends ViewRecord
{
    protected static string $resource = DailySalesReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('reconciliation_date')
                                    ->label('Fecha de Reconciliación')
                                    ->date('d/m/Y'),
                                
                                TextEntry::make('branch.name')
                                    ->label('Sucursal'),
                                
                                TextEntry::make('employee.name')
                                    ->label('Empleado'),
                            ]),
                    ]),

                Section::make('Resumen de Ventas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_sales')
                                    ->label('Ventas de Contado')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                                
                                TextEntry::make('total_credit_sales')
                                    ->label('Ventas de Crédito')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('warning'),
                                
                                TextEntry::make('total_sales')
                                    ->label('Total de Ventas')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                            ]),
                    ]),

                Section::make('Movimientos de Efectivo')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_received')
                                    ->label('Efectivo Recibido')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold),
                                
                                TextEntry::make('total_deposits')
                                    ->label('Depósitos')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('info'),
                                
                                TextEntry::make('total_collections')
                                    ->label('Cobros/Pagos')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('gray'),
                            ]),
                    ]),

                Section::make('Reconciliación')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_expected')
                                    ->label('Efectivo Esperado')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold),
                                
                                TextEntry::make('cash_difference')
                                    ->label('Diferencia')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')),
                                
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn ($state) => $state->getColor())
                                    ->icon(fn ($state) => $state->getIcon()),
                            ]),
                    ]),

                Section::make('Información Adicional')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notas')
                            ->placeholder('Sin notas')
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cashier.name')
                                    ->label('Cajero'),
                                
                                TextEntry::make('created_at')
                                    ->label('Fecha de Creación')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ]),
            ]);
    }
}
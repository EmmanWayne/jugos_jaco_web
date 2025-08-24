<?php

namespace App\Filament\Resources\DailySalesReconciliationResource\Pages;

use App\Enums\BankEnum;
use App\Filament\Resources\DailySalesReconciliationResource;
use App\Models\Deposit;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;

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
                                    ->date('d/m/Y')
                                    ->icon('heroicon-o-calendar'),
                                
                                TextEntry::make('branch.name')
                                    ->label('Sucursal')
                                    ->icon('heroicon-o-building-storefront'),
                                
                                TextEntry::make('employee.name')
                                    ->label('Empleado')
                                    ->icon('heroicon-o-user'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Resumen de Ventas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_sales')
                                    ->label('Ventas de Contado')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('success')
                                    ->icon('heroicon-o-banknotes'),
                                
                                TextEntry::make('total_credit_sales')
                                    ->label('Ventas de Crédito')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('warning')
                                    ->icon('heroicon-o-credit-card'),
                                
                                TextEntry::make('total_sales')
                                    ->label('Total de Ventas')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-shopping-cart'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Movimientos de Efectivo')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_received')
                                    ->label('Efectivo Recibido')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-banknotes'),
                                
                                TextEntry::make('total_deposits')
                                    ->label('Depósitos')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('info')
                                    ->icon('heroicon-o-building-library'),
                                
                                TextEntry::make('total_collections')
                                    ->label('Cobros/Pagos')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('gray')
                                    ->icon('heroicon-o-receipt-percent'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Reconciliación')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_expected')
                                    ->label('Efectivo Esperado')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-calculator'),
                                
                                TextEntry::make('cash_difference')
                                    ->label('Diferencia de Efectivo')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                                    ->icon('heroicon-o-currency-dollar'),
                                
                                TextEntry::make('deposit_difference')
                                    ->label('Diferencia de Depósitos')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                                    ->icon('heroicon-o-arrow-trending-up'),
                                
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn ($state) => $state->getColor())
                                    ->icon(fn ($state) => $state->getIcon()),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Depósitos Registrados')
                    ->schema([
                        RepeatableEntry::make('deposits')
                            ->hiddenLabel()
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('bank')
                                            ->label('Banco')
                                            ->formatStateUsing(fn ($state) => BankEnum::from($state)->name)
                                            ->badge()
                                            ->color(fn ($state) => BankEnum::bankColor(BankEnum::from($state)->name)),
                                            
                                        TextEntry::make('reference_number')
                                            ->label('Referencia')
                                            ->icon('heroicon-o-hashtag'),
                                            
                                        TextEntry::make('amount')
                                            ->label('Monto')
                                            ->money('HNL')
                                            ->icon('heroicon-o-banknotes'),
                                            
                                        TextEntry::make('created_at')
                                            ->label('Fecha')
                                            ->dateTime('d/m/Y H:i')
                                            ->icon('heroicon-o-calendar'),
                                    ]),
                            ])
                            ->columns(1)
                            ->contained(false)
                            ->state(function ($record) {
                                return Deposit::where('model_id', $record->id)
                                    ->get()
                                    ->map(function ($deposit) {
                                        return [
                                            'id' => $deposit->id,
                                            'bank' => $deposit->bank->value,
                                            'reference_number' => $deposit->reference_number,
                                            'amount' => $deposit->amount,
                                            'created_at' => $deposit->created_at,
                                        ];
                                    })
                                    ->toArray();
                            }),
                    ])
                    ->collapsible(),

                Section::make('Información Adicional')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notas')
                            ->placeholder('Sin notas')
                            ->columnSpanFull()
                            ->icon('heroicon-o-document-text'),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cashier.name')
                                    ->label('Cajero')
                                    ->icon('heroicon-o-user-circle'),
                                
                                TextEntry::make('created_at')
                                    ->label('Fecha de Creación')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
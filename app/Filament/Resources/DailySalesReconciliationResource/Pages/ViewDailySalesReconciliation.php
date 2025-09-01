<?php

namespace App\Filament\Resources\DailySalesReconciliationResource\Pages;

use App\Enums\BankEnum;
use App\Filament\Resources\DailySalesReconciliationResource;
use App\Models\Deposit;
use Filament\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
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
                // Header con información principal
                Section::make('📊 Resumen del Cuadre Diario')
                    ->description('Información general y estado del cuadre')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('reconciliation_date')
                                    ->label('📅 Fecha')
                                    ->date('d/m/Y')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('primary'),
                                
                                TextEntry::make('branch.name')
                                    ->label('🏪 Sucursal')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('info'),
                                
                                TextEntry::make('employee.full_name')
                                    ->label('👤 Empleado')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('gray'),
                                
                                TextEntry::make('status')
                                    ->label('📋 Estado')
                                    ->formatStateUsing(function ($state) {
                                        return $state->getLabel();
                                    })
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn ($state) => $state->getColor())
                                    ->icon(fn ($state) => $state->getIcon()),
                            ]),
                    ])
                    ->headerActions([
                        Action::make('print')
                            ->label('Imprimir')
                            ->icon('heroicon-o-printer')
                            ->color('gray')
                            ->openUrlInNewTab(),
                    ])
                    ->compact(),

                // Métricas principales de ventas
                Section::make('💰 Resumen de Ventas del Día')
                    ->description('Desglose detallado de todas las ventas realizadas')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Ventas al Contado con desglose
                                Group::make([
                                    TextEntry::make('total_cash_sales')
                                        ->label('💵 Ventas al Contado')
                                        ->money('HNL')
                                        ->weight(FontWeight::Bold)
                                        ->color('success'),
                                    
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('cash_sales')
                                                ->label('• Efectivo')
                                                ->money('HNL')
                                                ->placeholder('L 0.00')
                                                ->color('success')
                                                ->size('sm'),
                                            
                                            TextEntry::make('deposit_sales')
                                                ->label('• Depósito')
                                                ->money('HNL')
                                                ->placeholder('L 0.00')
                                                ->color('info')
                                                ->size('sm'),
                                        ]),
                                ]),
                                
                                // Cobros con desglose
                                Group::make([
                                    TextEntry::make('total_collections')
                                        ->label('📥 Total de Cobros')
                                        ->money('HNL')
                                        ->weight(FontWeight::Bold)
                                        ->color('purple'),
                                    
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('cash_collections')
                                                ->label('• Efectivo')
                                                ->money('HNL')
                                                ->placeholder('L 0.00')
                                                ->color('success')
                                                ->size('sm'),
                                            
                                            TextEntry::make('deposit_collections')
                                                ->label('• Depósito')
                                                ->money('HNL')
                                                ->placeholder('L 0.00')
                                                ->color('info')
                                                ->size('sm'),
                                        ]),
                                ]),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('total_credit_sales')
                                    ->label('💳 Ventas a Crédito')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('warning'),
                                
                                TextEntry::make('total_sales')
                                    ->label('🛒 Total de Ventas')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                            ]),
                    ])
                    ->compact(),

                // Movimientos de efectivo y depósitos
                Section::make('💸 Movimientos de Efectivo')
                    ->description('Control de efectivo recibido, depósitos realizados y cobros del día')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_cash_received')
                                    ->label('💵 Efectivo Recibido')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('success')
                                    ->extraAttributes(['class' => 'text-center p-3 bg-green-50 rounded-lg']),
                                
                                TextEntry::make('total_deposits')
                                    ->label('🏦 Depósitos Realizados')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('info')
                                    ->extraAttributes(['class' => 'text-center p-3 bg-blue-50 rounded-lg']),
                                
                                TextEntry::make('total_collections')
                                    ->label('📥 Cobros del Día')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('purple')
                                    ->extraAttributes(['class' => 'text-center p-3 bg-purple-50 rounded-lg']),
                            ]),
                    ])
                    ->compact(),

                // Análisis de reconciliación con alertas visuales
                Section::make('⚖️ Análisis de Reconciliación')
                    ->description('Comparación entre valores esperados y reales con indicadores de diferencias')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('total_cash_expected')
                                    ->label('🎯 Efectivo Esperado')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('primary')
                                    ->extraAttributes(['class' => 'text-center p-4 bg-blue-50 rounded-lg border border-blue-200']),
                                
                                TextEntry::make('total_deposit_expected')
                                    ->label('🎯 Depósitos Esperados')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('primary')
                                    ->extraAttributes(['class' => 'text-center p-4 bg-blue-50 rounded-lg border border-blue-200']),
                            ]),
                        
                        // Alertas de diferencias
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cash_difference')
                                    ->label('⚠️ Diferencia de Efectivo')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('xl')
                                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                                    ->extraAttributes(fn ($state) => [
                                        'class' => $state > 0 
                                            ? 'text-center p-4 bg-green-100 rounded-lg border-2 border-green-300' 
                                            : ($state < 0 
                                                ? 'text-center p-4 bg-red-100 rounded-lg border-2 border-red-300' 
                                                : 'text-center p-4 bg-gray-100 rounded-lg border-2 border-gray-300')
                                    ])
                                    ->formatStateUsing(fn ($state) => 
                                        ($state > 0 ? '+' : '') . 'L ' . number_format($state, 2)
                                    ),
                                
                                TextEntry::make('deposit_difference')
                                    ->label('⚠️ Diferencia de Depósitos')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold)
                                    ->size('xl')
                                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                                    ->extraAttributes(fn ($state) => [
                                        'class' => $state > 0 
                                            ? 'text-center p-4 bg-green-100 rounded-lg border-2 border-green-300' 
                                            : ($state < 0 
                                                ? 'text-center p-4 bg-red-100 rounded-lg border-2 border-red-300' 
                                                : 'text-center p-4 bg-gray-100 rounded-lg border-2 border-gray-300')
                                    ])
                                    ->formatStateUsing(fn ($state) => 
                                        ($state > 0 ? '+' : '') . 'L ' . number_format($state, 2)
                                    ),
                            ]),
                    ])
                    ->compact(),
                
                // Depósitos con diseño tabular mejorado
                Section::make('🏦 Depósitos Registrados')
                    ->description('Detalle de todos los depósitos bancarios realizados durante el día')
                    ->schema([
                        RepeatableEntry::make('deposits')
                            ->hiddenLabel()
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('bank')
                                                    ->label('🏛️ Banco')
                                                    ->formatStateUsing(fn ($state) => $state->getLabel())
                                                    ->badge()
                                                    ->size('lg')
                                                    ->color(fn ($state) => $state->getColor()),
                                                    
                                                TextEntry::make('reference_number')
                                                    ->label('📄 No. Referencia')
                                                    ->weight(FontWeight::Bold)
                                                    ->copyable()
                                                    ->copyMessage('Referencia copiada')
                                                    ->extraAttributes(['class' => 'font-mono']),
                                                    
                                                TextEntry::make('amount')
                                                    ->label('💰 Monto')
                                                    ->money('HNL')
                                                    ->weight(FontWeight::Bold)
                                                    ->size('lg')
                                                    ->color('success'),
                                                    
                                                TextEntry::make('created_at')
                                                    ->label('🕐 Fecha y Hora')
                                                    ->dateTime('d/m/Y H:i')
                                                    ->weight(FontWeight::Medium),
                                            ]),
                                    ])
                                    ->extraAttributes(['class' => 'p-4 bg-gray-50 rounded-lg border border-gray-200 mb-3']),
                            ])
                            ->columns(1)
                            ->contained(false)
                            ->state(function ($record) {
                                $deposits = $record->deposits()
                                    ->orderBy('created_at', 'desc')
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
                                
                                return empty($deposits) ? [[
                                    'bank' => '',
                                    'reference_number' => 'No hay depósitos registrados',
                                    'amount' => 0,
                                    'created_at' => null,
                                ]] : $deposits;
                            }),
                    ])
                    ->compact()
                    ->collapsible(),

                // Información adicional y metadatos
                Section::make('📝 Información Adicional')
                    ->description('Notas, observaciones y datos de auditoría del cuadre')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('📋 Notas y Observaciones')
                            ->placeholder('Sin notas adicionales')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'p-4 bg-yellow-50 rounded-lg border border-yellow-200'])
                            ->formatStateUsing(fn ($state) => $state ?: 'No se registraron notas para este cuadre'),
                        
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('cashier.name')
                                    ->label('👤 Cajero Responsable')
                                    ->weight(FontWeight::Bold)
                                    ->color('info')
                                    ->extraAttributes(['class' => 'p-3 bg-blue-50 rounded-lg']),
                                
                                TextEntry::make('created_at')
                                    ->label('🕐 Fecha de Creación')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->weight(FontWeight::Medium)
                                    ->extraAttributes(['class' => 'p-3 bg-gray-50 rounded-lg']),
                                
                                TextEntry::make('updated_at')
                                    ->label('🔄 Última Modificación')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->weight(FontWeight::Medium)
                                    ->extraAttributes(['class' => 'p-3 bg-gray-50 rounded-lg']),
                            ]),
                    ])
                    ->compact()
                    ->collapsible(),
            ]);
    }
}
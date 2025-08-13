<?php

namespace App\Filament\Resources\AccountReceivableResource\Pages;

use App\Enums\AccountReceivableStatusEnum;
use App\Filament\Actions\AddPaymentAction;
use App\Filament\Actions\MarkAsPaidAction;
use App\Filament\Actions\CancelAccountAction;
use App\Filament\Resources\AccountReceivableResource;
use App\Models\AccountReceivable;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewAccountReceivable extends ViewRecord
{
    protected static string $resource = AccountReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('heroicon-o-pencil'),

            AddPaymentAction::make(),

            MarkAsPaidAction::make(),

            CancelAccountAction::make(),

            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make('Información de la Cuenta')
                            ->schema([
                                Infolists\Components\TextEntry::make('sale.invoice_number')
                                    ->label('Número de Factura')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('Sin venta asociada')
                                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : 'Sin venta asociada'),

                                Infolists\Components\TextEntry::make('sale.client.name')
                                    ->label('Cliente')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-user')
                                    ->placeholder('Sin cliente')
                                    ->visible(fn (AccountReceivable $record): bool => $record->sale !== null),

                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label('Monto Total')
                                    ->money('HNL')
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('total_paid')
                                    ->label('Total Pagado')
                                    ->money('HNL')
                                    ->getStateUsing(fn (AccountReceivable $record): float => $record->total_paid)
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('remaining_balance')
                                    ->label('Saldo Pendiente')
                                    ->money('HNL')
                                    ->color(fn (AccountReceivable $record): string => $record->remaining_balance > 0 ? 'warning' : 'success'),

                                Infolists\Components\TextEntry::make('progress_percentage')
                                    ->label('Porcentaje de Pago')
                                    ->getStateUsing(fn (AccountReceivable $record): string => number_format($record->progress_percentage, 1) . '%')
                                    ->badge()
                                    ->color(fn (AccountReceivable $record): string => match (true) {
                                        $record->progress_percentage >= 100 => 'success',
                                        $record->progress_percentage >= 50 => 'warning',
                                        default => 'danger',
                                    }),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->formatStateUsing(fn (AccountReceivableStatusEnum $state): string => $state->getLabel())
                                    ->color(fn (AccountReceivableStatusEnum $state): string => $state->getColor())
                                    ->icon(fn (AccountReceivableStatusEnum $state): string => $state->getIcon()),

                                Infolists\Components\TextEntry::make('due_date')
                                    ->label('Fecha de Vencimiento')
                                    ->date('d/m/Y')
                                    ->color(fn (AccountReceivable $record): string => $record->isOverdue() ? 'danger' : 'gray')
                                    ->icon(fn (AccountReceivable $record): string => $record->isOverdue() ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-calendar'),
                            ])
                            ->columns(2),

                        Infolists\Components\Section::make('Información de la Venta')
                            ->schema([
                                Infolists\Components\TextEntry::make('sale.sale_date')
                                    ->label('Fecha de Venta')
                                    ->date('d/m/Y'),

                                Infolists\Components\TextEntry::make('sale.employee.name')
                                    ->label('Vendedor'),

                                Infolists\Components\TextEntry::make('sale.payment_type')
                                    ->label('Tipo de Pago')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('sale.total_amount')
                                    ->label('Total de la Venta')
                                    ->money('HNL'),

                                Infolists\Components\TextEntry::make('notes')
                                    ->label('Notas')
                                    ->placeholder('Sin notas')
                                    ->columnSpanFull()
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->collapsed()
                            ->visible(fn (AccountReceivable $record): bool => $record->sale !== null),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make('Fechas')
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Creado')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-plus-circle'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Última Modificación')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-pencil'),
                            ]),

                        Infolists\Components\Section::make('Estadísticas')
                            ->schema([
                                Infolists\Components\TextEntry::make('days_since_creation')
                                    ->label('Días desde creación')
                                    ->getStateUsing(fn (AccountReceivable $record): string => abs(round($record->created_at->diffInDays(now()))) . ' días'),

                                Infolists\Components\TextEntry::make('days_until_due')
                                    ->label('Días hasta vencimiento')
                                    ->getStateUsing(fn (AccountReceivable $record): string => $record->due_date
                                        ? abs(round($record->due_date->diffInDays(now(), false))) . ' días'
                                        : 'Sin fecha')
                                    ->color(fn (AccountReceivable $record): string => $record->isOverdue() ? 'danger' : 'gray'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public function getTitle(): string
    {
        return "Cuenta por Cobrar #{$this->record->id}";
    }
}

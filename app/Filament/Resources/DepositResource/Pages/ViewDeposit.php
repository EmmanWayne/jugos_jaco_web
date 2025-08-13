<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Enums\BankEnum;
use App\Filament\Resources\DepositResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDeposit extends ViewRecord
{
    protected static string $resource = DepositResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Depósito')
                    ->schema([
                        TextEntry::make('reference_number')
                            ->label('Número de referencia')
                            ->icon('heroicon-o-hashtag'),

                        TextEntry::make('amount')
                            ->label('Monto')
                            ->money('HNL')
                            ->icon('heroicon-o-banknotes'),

                        TextEntry::make('bank')
                            ->label('Banco')
                            ->badge()
                            ->color(fn ($state) => BankEnum::bankColor($state->name))
                            ->icon('heroicon-o-building-library'),

                        TextEntry::make('branch.name')
                            ->label('Sucursal')
                            ->icon('heroicon-o-map-pin'),

                        TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->icon('heroicon-o-document-text')
                            ->placeholder('Sin descripción'),
                    ])
                    ->columns(2),

                Section::make('Información del Sistema')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de creación')
                            ->dateTime('d/m/Y H:i:s')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->dateTime('d/m/Y H:i:s')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2)
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

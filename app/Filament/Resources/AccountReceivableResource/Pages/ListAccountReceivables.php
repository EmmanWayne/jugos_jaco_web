<?php

namespace App\Filament\Resources\AccountReceivableResource\Pages;

use App\Enums\AccountReceivableStatusEnum;
use App\Filament\Resources\AccountReceivableResource;
use App\Models\AccountReceivable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAccountReceivables extends ListRecords
{
    protected static string $resource = AccountReceivableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Cuenta por Cobrar')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(AccountReceivable::count()),

            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AccountReceivableStatusEnum::PENDING))
                ->badge(AccountReceivable::where('status', AccountReceivableStatusEnum::PENDING)->count())
                ->badgeColor('warning'),

            'paid' => Tab::make('Pagadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AccountReceivableStatusEnum::PAID))
                ->badge(AccountReceivable::where('status', AccountReceivableStatusEnum::PAID)->count())
                ->badgeColor('success'),

            'overdue' => Tab::make('Vencidas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('due_date', '<', now())->where('status', AccountReceivableStatusEnum::PENDING))
                ->badge(AccountReceivable::where('due_date', '<', now())->where('status', AccountReceivableStatusEnum::PENDING)->count())
                ->badgeColor('danger'),

            'due_soon' => Tab::make('Vencen Pronto')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('due_date', [now(), now()->addWeek()])->where('status', AccountReceivableStatusEnum::PENDING))
                ->badge(AccountReceivable::whereBetween('due_date', [now(), now()->addWeek()])->where('status', AccountReceivableStatusEnum::PENDING)->count())
                ->badgeColor('warning'),

            'cancelled' => Tab::make('Canceladas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AccountReceivableStatusEnum::CANCELLED))
                ->badge(AccountReceivable::where('status', AccountReceivableStatusEnum::CANCELLED)->count())
                ->badgeColor('gray'),
        ];
    }
}

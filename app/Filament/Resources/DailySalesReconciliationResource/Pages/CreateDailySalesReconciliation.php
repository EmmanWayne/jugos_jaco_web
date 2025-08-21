<?php

namespace App\Filament\Resources\DailySalesReconciliationResource\Pages;

use App\Filament\Resources\DailySalesReconciliationResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateDailySalesReconciliation extends Page
{
    protected static string $resource = DailySalesReconciliationResource::class;
    
    protected static string $view = 'filament.resources.daily-sales-reconciliation-resource.pages.create-daily-sales-reconciliation';
    
    protected static ?string $title = 'Cuadre Diario de Ventas';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}
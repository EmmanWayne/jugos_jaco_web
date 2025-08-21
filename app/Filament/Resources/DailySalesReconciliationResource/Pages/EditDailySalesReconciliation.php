<?php

namespace App\Filament\Resources\DailySalesReconciliationResource\Pages;

use App\Filament\Resources\DailySalesReconciliationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailySalesReconciliation extends EditRecord
{
    protected static string $resource = DailySalesReconciliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalcular totales automáticamente al editar
        $data['total_sales'] = ($data['total_cash_sales'] ?? 0) + ($data['total_credit_sales'] ?? 0);
        $data['total_cash_expected'] = ($data['total_cash_sales'] ?? 0) + ($data['total_collections'] ?? 0) - ($data['total_deposits'] ?? 0);
        $data['cash_difference'] = ($data['total_cash_received'] ?? 0) - $data['total_cash_expected'];
        
        return $data;
    }
}
<?php

namespace App\Filament\Resources\InvoicesSeriesResource\Pages;

use App\Filament\Resources\InvoicesSeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoicesSeries extends EditRecord
{
    protected static string $resource = InvoicesSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

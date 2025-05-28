<?php

namespace App\Filament\Resources\InvoicesSeriesResource\Pages;

use App\Filament\Resources\InvoicesSeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoicesSeries extends ViewRecord
{
    protected static string $resource = InvoicesSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

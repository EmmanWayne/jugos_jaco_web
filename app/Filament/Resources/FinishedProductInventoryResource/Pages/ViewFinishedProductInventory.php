<?php

namespace App\Filament\Resources\FinishedProductInventoryResource\Pages;

use App\Filament\Resources\FinishedProductInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinishedProductInventory extends ViewRecord
{
    protected static string $resource = FinishedProductInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

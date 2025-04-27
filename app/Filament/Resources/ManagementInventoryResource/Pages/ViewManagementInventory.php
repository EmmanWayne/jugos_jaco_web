<?php

namespace App\Filament\Resources\ManagementInventoryResource\Pages;

use App\Filament\Resources\ManagementInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewManagementInventory extends ViewRecord
{
    protected static string $resource = ManagementInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ManagementInventoryResource\Pages;

use App\Filament\Resources\ManagementInventoryResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditManagementInventory extends EditRecord
{
    protected static string $resource = ManagementInventoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

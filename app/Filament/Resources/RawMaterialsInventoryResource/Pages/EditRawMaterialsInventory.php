<?php

namespace App\Filament\Resources\RawMaterialsInventoryResource\Pages;

use App\Filament\Resources\RawMaterialsInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRawMaterialsInventory extends EditRecord
{
    protected static string $resource = RawMaterialsInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

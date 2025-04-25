<?php

namespace App\Filament\Resources\RawMaterialsInventoryResource\Pages;

use App\Filament\Resources\RawMaterialsInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRawMaterialsInventories extends ListRecords
{
    protected static string $resource = RawMaterialsInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

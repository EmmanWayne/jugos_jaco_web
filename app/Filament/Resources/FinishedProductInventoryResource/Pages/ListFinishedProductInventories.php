<?php

namespace App\Filament\Resources\FinishedProductInventoryResource\Pages;

use App\Filament\Resources\FinishedProductInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinishedProductInventories extends ListRecords
{
    protected static string $resource = FinishedProductInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

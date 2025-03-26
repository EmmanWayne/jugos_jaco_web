<?php

namespace App\Filament\Resources\TypePriceResource\Pages;

use App\Filament\Resources\TypePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypePrices extends ListRecords
{
    protected static string $resource = TypePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

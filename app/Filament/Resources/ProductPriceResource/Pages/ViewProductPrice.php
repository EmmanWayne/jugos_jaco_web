<?php

namespace App\Filament\Resources\ProductPriceResource\Pages;

use App\Filament\Resources\ProductPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductPrice extends ViewRecord
{
    protected static string $resource = ProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

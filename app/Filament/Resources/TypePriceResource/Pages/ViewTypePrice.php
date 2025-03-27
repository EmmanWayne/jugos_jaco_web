<?php

namespace App\Filament\Resources\TypePriceResource\Pages;

use App\Filament\Resources\TypePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTypePrice extends ViewRecord
{
    protected static string $resource = TypePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

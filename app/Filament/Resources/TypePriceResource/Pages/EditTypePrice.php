<?php

namespace App\Filament\Resources\TypePriceResource\Pages;

use App\Filament\Resources\TypePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypePrice extends EditRecord
{
    protected static string $resource = TypePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

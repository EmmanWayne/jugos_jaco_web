<?php

namespace App\Filament\Resources\AssignedProductResource\Pages;

use App\Filament\Resources\AssignedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssignedProduct extends EditRecord
{
    protected static string $resource = AssignedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

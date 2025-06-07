<?php

namespace App\Filament\Resources\AssignedProductResource\Pages;

use App\Filament\Resources\AssignedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssignedProduct extends ViewRecord
{
    protected static string $resource = AssignedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

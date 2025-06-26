<?php

namespace App\Filament\Resources\AssignedProductResource\Pages;

use App\Filament\Resources\AssignedProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssignedProducts extends ListRecords
{
    protected static string $resource = AssignedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

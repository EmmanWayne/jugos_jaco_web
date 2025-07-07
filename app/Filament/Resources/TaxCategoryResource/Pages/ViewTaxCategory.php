<?php

namespace App\Filament\Resources\TaxCategoryResource\Pages;

use App\Filament\Resources\TaxCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTaxCategory extends ViewRecord
{
    protected static string $resource = TaxCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

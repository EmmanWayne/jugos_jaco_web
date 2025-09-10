<?php

namespace App\Filament\Resources\ProductReturnResource\Pages;

use App\Filament\Resources\ProductReturnResource;
use App\Services\ProductReturnService;
use Filament\Resources\Pages\CreateRecord;

class CreateProductReturn extends CreateRecord
{
    protected static string $resource = ProductReturnResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function afterCreate(): void
    {
        $productReturnService = new ProductReturnService();
        $productReturnService->registerInventoryMovement($this->getRecord());
    }
}
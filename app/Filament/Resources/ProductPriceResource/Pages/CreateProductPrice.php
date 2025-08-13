<?php

namespace App\Filament\Resources\ProductPriceResource\Pages;

use App\Filament\Resources\ProductPriceResource;
use App\Services\ExceptionHandlerService;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProductPrice extends CreateRecord
{
    protected static string $resource = ProductPriceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return static::getModel()::create($data);
        } catch (Exception $e) {
            ExceptionHandlerService::handle($e);
            $this->halt();

            throw $e;
        }
    }
}

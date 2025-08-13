<?php

namespace App\Filament\Resources\ProductUnitResource\Pages;

use App\Filament\Resources\ProductUnitResource;
use App\Services\ExceptionHandlerService;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProductUnit extends CreateRecord
{
    protected static string $resource = ProductUnitResource::class;

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

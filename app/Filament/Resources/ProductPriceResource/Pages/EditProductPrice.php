<?php

namespace App\Filament\Resources\ProductPriceResource\Pages;

use App\Filament\Resources\ProductPriceResource;
use App\Services\ExceptionHandlerService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProductPrice extends EditRecord
{
    protected static string $resource = ProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);
            return $record;
        } catch (\Exception $e) {
            ExceptionHandlerService::handle($e);
            $this->halt();

            throw $e;
        }
    }
}

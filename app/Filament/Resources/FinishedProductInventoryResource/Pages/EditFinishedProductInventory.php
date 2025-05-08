<?php

namespace App\Filament\Resources\FinishedProductInventoryResource\Pages;

use App\Filament\Resources\FinishedProductInventoryResource;
use App\Models\FinishedProductInventory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class EditFinishedProductInventory extends EditRecord
{
    protected static string $resource = FinishedProductInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}

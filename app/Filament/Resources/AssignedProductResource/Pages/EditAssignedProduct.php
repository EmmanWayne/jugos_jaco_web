<?php

namespace App\Filament\Resources\AssignedProductResource\Pages;

use App\Filament\Resources\AssignedProductResource;
use App\Filament\Support\FilamentNotification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssignedProduct extends EditRecord
{
    protected static string $resource = AssignedProductResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->using(function ($record, $action) {
                    if ($record->details()->exists()) {
                        FilamentNotification::warning(
                            title: 'Asignación de Productos',
                                body: 'No se puede eliminar la asignación de productos debido a que tiene productos asignados.',
                        );

                        $action->cancel();
                    }

                    $record->delete();
                }),
        ];
    }
}

<?php

namespace App\Filament\Resources\AssignedProductResource\Pages;

use App\Filament\Resources\AssignedProductResource;
use App\Filament\Support\FilamentNotification;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssignedProduct extends ViewRecord
{
    protected static string $resource = AssignedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => $this->record->date->format('Y-m-d') === now()->format('Y-m-d')),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if ($this->record->date->format('Y-m-d') !== now()->format('Y-m-d')) {
            FilamentNotification::info(
                title: 'Información',
                body: 'No se pueden modificar asignaciones de productos de fechas anteriores o futuras. Solo se pueden editar asignaciones del día de hoy.',
            );
        }
    }
}

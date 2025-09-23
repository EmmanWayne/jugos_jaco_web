<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Actions\TransferClientsAction;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeClientsStatsWidget;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeClientsTableWidget;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TransferClientsAction::make(),
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EmployeeClientsStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EmployeeClientsTableWidget::class,
        ];
    }
}

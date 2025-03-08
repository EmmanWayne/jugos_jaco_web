<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Section;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            Section::make('Fotografías del negocio')
                ->description('En esta sección se muestran las fotografías del negocio.')
                ->columns(4)
                ->schema([
                    \App\Filament\Resources\ClientResource\Widgets\BusinessImagesWidget::class,
                ]),

        ];
    }
}

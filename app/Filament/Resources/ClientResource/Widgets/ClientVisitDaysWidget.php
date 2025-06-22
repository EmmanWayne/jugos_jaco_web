<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use Filament\Widgets\Widget;

class ClientVisitDaysWidget extends Widget
{
    protected static string $view = 'filament.resources.client-resource.widgets.client-visit-days-widget';

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function getVisitDays()
    {
        // Asumiendo que tienes una relación visitDays en el modelo Client
        return $this->record->visitDays ?? [];
    }

    public static function getMaxWith(): string
    {
        return 'full';
    }
}
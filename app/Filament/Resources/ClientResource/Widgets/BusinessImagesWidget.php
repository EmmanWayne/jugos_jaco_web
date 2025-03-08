<?php
namespace App\Filament\Resources\ClientResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;

class BusinessImagesWidget extends Widget
{
    protected static string $view = 'filament.resources.client-resource.widgets.business-images-widget';

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function getImages()
    {
        return $this->record->businessImages;
    }

    public static function getMaxWith(): string
    {
        return 'full';
    }
}
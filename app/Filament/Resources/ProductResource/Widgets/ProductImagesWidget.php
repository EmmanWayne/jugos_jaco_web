<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;

class ProductImagesWidget extends Widget
{
    protected static string $view = 'filament.resources.product-resource.widgets.product-images-widget';

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function getImages()
    {
        return $this->record->profileImage;
    }

    public static function getMaxWith(): string
    {
        return 'full';
    }
}

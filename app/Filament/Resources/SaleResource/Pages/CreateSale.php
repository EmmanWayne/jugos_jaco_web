<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Filament\Resources\SaleResource;
use App\Models\Client;
use App\Services\ProductService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateSale extends Page
{
    protected static string $resource = SaleResource::class;

    protected static string $view = 'filament.resources.sale-resource.pages.create-sale';
}
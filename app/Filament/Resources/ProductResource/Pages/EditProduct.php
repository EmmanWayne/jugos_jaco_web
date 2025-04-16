<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function (Model $record) {
                    $this->productService->deleteProductImage($record);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $imagePath = $data['product_image'] ?? null;

        if ($imagePath) {
            $this->productService->uploadProductImage($imagePath, $record);
        }

        $record->update($data);

        return $record;
    }
}

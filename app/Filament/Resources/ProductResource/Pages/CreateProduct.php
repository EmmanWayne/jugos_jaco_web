<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $imagePath = $data['product_image'] ?? null;

        $record = static::getModel()::create($data);

        if ($imagePath) {
            $this->productService->uploadProductImage($imagePath, $record);
        }

        return $record;
    }
}

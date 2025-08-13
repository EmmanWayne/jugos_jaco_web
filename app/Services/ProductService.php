<?php

namespace App\Services;

use App\Enums\StoragePath;
use App\Models\FinishedProductInventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Nette\NotImplementedException;

class ProductService
{

    /**
     * Upload product image to storage.
     *
     * @param mixed $image The image to upload.
     * @param mixed $record The product record.
     * @return void
     */
    public function uploadProductImage($image, $record): void
    {
        if (!$image || !$record) return;

        // Check if the record already has an image and delete it
        if ($record?->profileImage) {
            $existingImagePath = $record->profileImage->path;
            if (Storage::disk(StoragePath::ROOT_DIRECTORY->value)->exists($existingImagePath)) {
                Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($existingImagePath);
            }
        }

        $fullPath = Storage::disk(StoragePath::ROOT_DIRECTORY->value)->path($image);

        $file = new \Illuminate\Http\UploadedFile(
            $fullPath,
            basename($image),
            Storage::disk(StoragePath::ROOT_DIRECTORY->value)->mimeType($image),
            null,
            true
        );

        $uniqid = uniqid();
        $fileName = $uniqid . "_" . $record->id . ".jpg";

        $path = Storage::disk(StoragePath::ROOT_DIRECTORY->value)
            ->putFileAs(
                StoragePath::PRODUCTS_IMAGES->value,
                $file,
                $fileName
            );

        $record->profileImage()->updateOrCreate(
            ['type' => 'product'],
            ['path' => $path]
        );

        // Delete the temp image after uploading and creating or updating the record
        if (Storage::disk(StoragePath::ROOT_DIRECTORY->value)->exists($image)) {
            Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($image);
        }
    }

    /**
     * Delete product image from storage.
     *
     * @param mixed $record The product record.
     * @return void
     */
    public function deleteProductImage($record): void
    {
        if ($record?->profileImage) {
            $existingImagePath = $record->profileImage->path;
            if (Storage::disk(StoragePath::ROOT_DIRECTORY->value)->exists($existingImagePath)) {
                Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($existingImagePath);
                $record->profileImage()->delete();
            }
        }
    }

    public function getSearchProduct($search, $typePriceId, $branchId): Collection
    {
        $productsResult = FinishedProductInventory::with([
            'product' => function ($query) {
                $query->select('id', 'name', 'code', 'is_active')
                    ->isActive(true);
            },
            'product.productPrices' => function ($query) use ($typePriceId) {
                $query->select('id', 'product_id', 'type_price_id', 'tax_category_id', 'price', 'price_include_tax', 'product_unit_id')
                    ->typePrice($typePriceId)
                    ->with([
                        'taxCategory:id,name,rate',
                    ]);
            },
            'product.productUnits' => function ($query) {
                $query->select('id', 'is_base_unit', 'product_id', 'unit_id', 'conversion_factor', 'is_active')
                    ->baseUnit(true)
                    ->with([
                        'unit:id,name,abbreviation',
                    ]);
            }
        ])
            ->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            })
            ->where('stock', '>', 0)
            ->where('branch_id', $branchId)
            ->get();

        return $productsResult;
    }
}

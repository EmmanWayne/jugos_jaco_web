<?php

namespace App\Services;

use App\Enums\StoragePath;
use App\Models\AssignedProduct;
use App\Models\FinishedProductInventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Metodo encargado de obtener los productos que tenga asignado un empleado
     * @param int $employeeId
     * @return AssignedProduct
     */
    public function getAssignedProduct($employeeId): AssignedProduct
    {
        $assignedProducts = AssignedProduct::todayAssignments()
            ->with(['details.product'])
            ->where('employee_id', $employeeId)
            ->first();

        return $assignedProducts;
    }

    /**
     * Metodo encargado de buscar productos
     * @param string $search
     * @param int $typePriceId
     * @param int $branchId
     * @return Collection
     */
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

    /**
     * Metodo encargado de obtener los productos asignados a un empleado
     * @param string $search
     * @param int $typePriceId
     * @param int $branchId
     * @param int $employeeId
     * @return Collection
     */
    public function getProducts($search, $typePriceId, $employeeId)
    {
        $assignedProducts = AssignedProduct::todayAssignments()
            ->with([
                'details' => function ($query) use ($search) {
                    $query->whereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    });
                },
                'details.product' => function ($query) {
                    $query->select('id', 'name', 'code', 'description', 'is_active')
                        ->isActive(true);
                },
                'details.product.productPrices' => function ($query) use ($typePriceId) {
                    $query->select('id', 'product_id', 'type_price_id', 'tax_category_id', 'price', 'price_include_tax', 'product_unit_id')
                        ->typePrice($typePriceId)
                        ->with([
                            'taxCategory:id,name,rate',
                        ]);
                },
                'details.product.productUnits' => function ($query) {
                    $query->select('id', 'is_base_unit', 'product_id', 'unit_id', 'conversion_factor', 'is_active')
                        ->baseUnit(true)
                        ->with([
                            'unit:id,name,abbreviation',
                        ]);
                }
            ])
            ->where('employee_id', $employeeId)
            ->first();

        // Si no hay productos asignados, retornar colección vacía
        if (!$assignedProducts || $assignedProducts->details->isEmpty()) return collect([]);

        // Mapear los detalles de productos asignados para incluir toda la información relevante
        $result = $assignedProducts->details->map(function ($detail) {
            // Obtener el stock disponible usando el accessor o calculándolo manualmente
            $availableStock = $detail->stock ?? ($detail->quantity - ($detail->sale_quantity ?? 0));

            if ($availableStock <= 0) return null;

            $productPrice = $detail->product->productPrices->first();
            $productUnit = $detail->product->productUnits->first();

            return [
                'id' => $detail->product_id,
                'name' => $detail->product->name,
                'code' => $detail->product->code,
                'description' => $detail->product->description,
                'quantity' => [
                    'assigned' => (int)$detail->quantity,
                    'sold' => (int)($detail->sale_quantity ?? 0),
                    'available' => (int)$availableStock,
                ],
                'unit' => $productUnit->unit->name,
                'unitAbbreviation' => $productUnit->unit->abbreviation,
                'priceWithTax' => (float)$productPrice->getPriceWithTax(),
                'priceWithoutTax' => (float)$productPrice->getPriceWithoutTax(),
                'taxAmount' => (float)$productPrice->getTaxAmount(),
                'product_price_id' => $productPrice->id
            ];
        })
            ->filter() 
            ->values();

        return $result;
    }
}

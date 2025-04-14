<?php

namespace App\Services;

use App\Enums\StoragePath;
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
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\StoragePath;
use App\Models\ResourceMedia;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class ResourceMediaController extends Controller
{
    use ApiResponse;

    /**
     * Delete a media.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMedia($id)
    {
        try {
            $media = ResourceMedia::findOrFail($id);
            Storage::disk(StoragePath::ROOT_DIRECTORY->value)->delete($media->path);

            $media->delete();

            return $this->successResponse(
                [],
                'Media eliminado correctamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e, 404, 'Media no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e, 400, 'Error al eliminar el media');
        }
    }
}

<!-- filepath: resources/views/filament/resources/client-resource/widgets/business-images-widget.blade.php -->
<div class="p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($this->getImages() as $image)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="{{ Storage::url($image->path) }}" alt="Business Image" class="w-full h-48 object-cover">
            </div>
        @endforeach
    </div>
</div>
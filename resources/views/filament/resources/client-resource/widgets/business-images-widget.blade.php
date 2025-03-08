<!-- filepath: resources/views/filament/resources/client-resource/widgets/business-images-widget.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($this->getImages() as $image)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <img src="{{ Storage::url($image->path) }}" alt="Business Image" class="w-full h-48 object-cover">
        </div>
    @endforeach
</div>
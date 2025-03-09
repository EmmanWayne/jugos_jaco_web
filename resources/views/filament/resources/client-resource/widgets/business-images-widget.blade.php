<!-- filepath: resources/views/filament/resources/client-resource/widgets/business-images-widget.blade.php -->
<x-filament::widget class="!col-span-full w-full max-w-full">
    <x-filament::card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold tracking-tight">Fotos del Negocio</h2>
                    <p class="text-sm text-gray-600">
                        Esta sección muestra las fotos del negocio del cliente.
                    </p>
                </div>
            </div>
            <div class="w-full border-t border-gray-200 mb-4"></div>

            @if($this->getImages()->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($this->getImages() as $image)
                        <div class="relative group">
                            <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-gray-100">
                                <img 
                                    src="{{ Storage::url($image->path) }}" 
                                    alt="Imagen del negocio"
                                    class="w-full h-full object-cover transition-all duration-300 group-hover:scale-105"
                                    loading="lazy"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center p-6 text-gray-500">
                    <span>No hay imágenes disponibles</span>
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-center">
            @if ($record->profileImage)
                <img src="{{ Storage::url($record->profileImage->path) }}" alt="Imagen del producto"
                    class="max-w-md rounded-lg">
            @else
                <img src="{{ asset('/images/producto.png') }}" alt="Imagen por defecto" class="max-w-md rounded-lg">
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>

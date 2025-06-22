{{-- filepath: resources/views/filament/resources/client-resource/widgets/client-visit-days-widget.blade.php --}}
<x-filament::widget class="!col-span-full w-full max-w-full">
    <x-filament::card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold tracking-tight">Días de Visita</h2>
                    <p class="text-sm text-gray-600">
                        Esta sección muestra los días de visita asignados al cliente.
                    </p>
                </div>
            </div>
            <div class="w-full mb-4 border-t border-gray-200"></div>

            @if(count($this->getVisitDays()))
                <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($this->getVisitDays() as $day)
                        <li class="flex items-center px-4 py-2 font-medium text-gray-700 bg-gray-100 rounded-lg">
                            {{ is_object($day) ? ($day->visit_day ?? $day->day ?? $day->name) : $day }}
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex items-center justify-center p-6 text-gray-500">
                    <span>No hay días de visita asignados</span>
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>

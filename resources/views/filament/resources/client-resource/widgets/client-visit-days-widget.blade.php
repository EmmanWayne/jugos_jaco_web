{{-- filepath: resources/views/filament/resources/client-resource/widgets/client-visit-days-widget.blade.php --}}
<x-filament::widget class="!col-span-full w-full max-w-full">
    <x-filament::card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold tracking-tight">Días de Visita</h2>
                    <p class="text-sm text-gray-600">
                        Esta sección muestra los días de visita asignados al cliente con su posición.
                    </p>
                </div>
            </div>
            <div class="w-full mb-4 border-t border-gray-200"></div>

            @if(count($this->getVisitDays()))
                <ul class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($this->getVisitDays() as $day)
                        <li class="flex items-center justify-between px-4 py-3 font-medium text-gray-700 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-black text-sm font-bold rounded-full">
                                    {{ $day->position }}
                                </span>
                                <span>{{ $day->visit_day }}</span>
                            </div>
                            <span class="text-xs text-gray-500 font-normal">
                                Posición. {{ $day->position }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center p-4 text-gray-500 text-sm">
                    <svg class="w-6 h-6 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="font-medium">No hay días de visita asignados</span>
                </div>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>

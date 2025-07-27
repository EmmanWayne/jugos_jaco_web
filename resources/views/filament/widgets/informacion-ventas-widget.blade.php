{{-- filepath: resources/views/filament/widgets/informacion-ventas-widget.blade.php --}}
<div class="p-6 rounded-2xl shadow-lg bg-gradient-to-r from-amber-100 via-yellow-50 to-orange-100">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-amber-600 drop-shadow-lg mb-1">
            {{ $this->getHeading() }}
        </h2>
        <p class="text-base font-semibold text-orange-500 mt-2 mb-4">
            {{ $this->getDescription() }}
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($this->getCards() as $card)
            {!! $card->render() !!}
        @endforeach
    </div>
</div>

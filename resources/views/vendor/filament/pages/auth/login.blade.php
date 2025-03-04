<x-filament-panels::page.simple>
    <div class="flex w-full flex-col items-center space-y-8">
        <div class="flex justify-center">
            <img 
                src="{{ asset('images/logo.png') }}" 
                alt="Logo Jugos Jaco"
                class="h-96" {{-- 384px de alto --}}
                style="width: auto; max-width: 90vw;"
            />
        </div>

        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="true"
            />
        </x-filament-panels::form>
    </div>
</x-filament-panels::page.simple>

<style>
    body {
        background-color: #df4c3c !important;
    }

    .fi-simple-page {
        margin-top: 2rem !important;
    }

    .fi-simple-card {
        background-color: white !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        border-radius: 1rem !important;
        padding: 2rem !important;
    }
</style> 
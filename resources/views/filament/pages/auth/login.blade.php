@php
    $logo = asset('images/logo.png');
@endphp

<div class="fi-simple-main flex min-h-screen flex-col items-center bg-[#df4c3c]">
    <div class="flex flex-col items-center justify-center w-full">
        <div class="my-16">
            <img src="{{ $logo }}" alt="Logo Jugos Jaco" class="w-auto h-80" style="max-width: 90vw;" />
        </div>

        <div class="fi-simple-card w-[28rem] rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-950/5">
            <h2 class="text-2xl font-bold tracking-tight text-center text-gray-950">
                {{ __('filament-panels::pages/auth/login.title') }}
            </h2>

            <form wire:submit="authenticate" class="mt-8 space-y-8">
                {{ $this->form }}

                <x-filament::button type="submit" form="authenticate" class="w-full">
                    {{ __('filament-panels::pages/auth/login.form.actions.authenticate.label') }}
                </x-filament::button>
            </form>
        </div>
    </div>
</div>

<style>
    .fi-simple-card {
        background-color: white !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
</style>

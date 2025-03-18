@php
    use App\Models\SystemSetting;
    use Illuminate\Support\Facades\Storage;
    use App\Helpers\ThemeHelper;
    use App\Helpers\AppHelper;
    $settings = SystemSetting::getSettings();
@endphp

<x-filament-panels::page.simple class="fi-auth-page">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            background: linear-gradient(135deg, {{ ThemeHelper::getThemeColorVariant('darker') }} 0%, {{ ThemeHelper::getThemeColor() }} 50%, {{ ThemeHelper::getThemeColorVariant('darker') }} 100%) !important;
            font-family: 'Poppins', sans-serif !important;
            min-height: 100vh !important;
        }

        .fi-simple-page {
            padding-top: 0 !important;
            min-height: auto !important;
            display: flex !important;
            align-items: flex-start !important;
            justify-content: center !important;
        }

        .fi-simple-card {
            background-color: white !important;
            box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 1rem !important;
            padding: 1rem !important;
            margin: 0.25rem auto !important;
            font-family: 'Poppins', sans-serif !important;
            width: min(26rem, 92vw) !important;
            transform: translateY(0) !important;
            transition: transform 0.2s, box-shadow 0.2s !important;
        }

        .fi-simple-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 20px 30px -8px rgba(0, 0, 0, 0.2), 0 10px 15px -3px rgba(0, 0, 0, 0.15) !important;
        }

        .fi-btn {
            background-color: {{ ThemeHelper::getThemeColor() }} !important;
            border-color: {{ ThemeHelper::getThemeColor() }} !important;
            height: 45px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            padding: 0.75rem !important;
            border-radius: 0.5rem !important;
            font-family: 'Poppins', sans-serif !important;
            letter-spacing: 0.5px !important;
        }

        .fi-btn:hover {
            background-color: {{ ThemeHelper::getThemeColorVariant('darker') }} !important;
            border-color: {{ ThemeHelper::getThemeColorVariant('darker') }} !important;
        }

        .fi-auth-page .fi-simple-header {
            display: none !important;
        }

        /* Estilos para los inputs */
        .fi-input-wrp {
            background-color: #f8f8f8 !important;
            border: 2px solid #e5e5e5 !important;
            border-radius: 0.5rem !important;
            height: 45px !important;
            margin-top: 0.25rem !important;
            transition: all 0.2s ease-in-out !important;
        }

        .fi-input-wrp:focus-within {
            border-color: {{ ThemeHelper::getThemeColor() }} !important;
            box-shadow: 0 0 0 1px {{ ThemeHelper::getThemeColor() }} !important;
        }

        .fi-input {
            background-color: transparent !important;
            color: #1a1a1a !important;
            font-size: 0.95rem !important;
            padding: 0.75rem !important;
            height: 100% !important;
            font-family: 'Poppins', sans-serif !important;
        }

        .fi-input:focus {
            border-color: transparent !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Estilos para las etiquetas */
        .fi-fo-label {
            color: #4a4a4a !important;
            font-weight: 500 !important;
            font-size: 0.9rem !important;
            margin-bottom: 0.25rem !important;
            font-family: 'Poppins', sans-serif !important;
            letter-spacing: 0.3px !important;
        }

        /* Estilos para el texto de placeholder */
        .fi-input::placeholder {
            color: #9ca3af !important;
            font-family: 'Poppins', sans-serif !important;
        }

        /* Estilos para el checkbox */
        .fi-checkbox-input {
            width: 1.2rem !important;
            height: 1.2rem !important;
            border: 2px solid {{ ThemeHelper::getThemeColor() }} !important;
            border-radius: 0.25rem !important;
            cursor: pointer !important;
        }

        .fi-checkbox-input:checked {
            background-color: {{ ThemeHelper::getThemeColor() }} !important;
            border-color: {{ ThemeHelper::getThemeColor() }} !important;
        }

        .fi-checkbox-label {
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            color: #4a4a4a !important;
            cursor: pointer !important;
            font-family: 'Poppins', sans-serif !important;
        }

        /* Contenedor del checkbox */
        .fi-form-component-checkbox {
            padding: 0.25rem 0 !important;
            margin: 0 !important;
        }

        /* Estilo para el título */
        .login-title {
            color: #1a1a1a !important;
            font-size: 1.35rem !important;
            font-weight: 600 !important;
            margin: 0.5rem 0 0.25rem 0 !important;
            font-family: 'Poppins', sans-serif !important;
            letter-spacing: 0.5px !important;
            text-align: center !important;
            width: 100% !important;
        }

        /* Contenedor del logo */
        .logo-container {
            margin: 0 auto !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            width: 100% !important;
            max-width: min(26rem, 92vw) !important;
        }

        /* Ajuste para la imagen del logo */
        .logo-image {
            display: block !important;
            margin: 0 auto !important;
            padding: 0 !important;
            height: min(280px, 35vh) !important;
            width: min(350px, 92vw) !important;
            object-fit: contain !important;
            will-change: transform !important;
            border-radius: 1rem !important;
        }

        /* Ajuste del espaciado del formulario */
        .form-spacing {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.75rem !important;
        }

        /* Contenedor del botón */
        .button-container {
            margin-top: 0.25rem !important;
        }

        @media (max-width: 640px) {
            .fi-simple-card {
                padding: 0.875rem !important;
                margin: 0.25rem auto !important;
            }

            .logo-image {
                height: min(250px, 32vh) !important;
            }

            .login-title {
                font-size: 1.25rem !important;
                margin: 0.375rem 0 0.25rem 0 !important;
            }
        }

        @media (max-width: 480px) {
            .fi-simple-card {
                padding: 0.75rem !important;
                margin: 0.25rem auto !important;
            }

            .logo-image {
                height: min(220px, 30vh) !important;
            }

            .login-title {
                font-size: 1.2rem !important;
                margin: 0.25rem 0 0.25rem 0 !important;
            }
        }
    </style>

    <div class="flex w-full flex-col items-center">
        <div class="flex flex-col items-center logo-container">
            <img 
                src="{{ $settings?->logo_url ?? asset('images/logo.png') }}" 
                alt="{{ AppHelper::getAppName() }}"
                class="logo-image"
                loading="eager"
                decoding="async"
                fetchpriority="high"
            />
            <h1 class="login-title">Iniciar Sesión</h1>
        </div>

        <div class="fi-simple-card">
            <form wire:submit="authenticate" class="form-spacing">
                {{ $this->form }}

                <div class="button-container">
                    <x-filament::button type="submit" form="authenticate" class="w-full">
                        {{ __('filament-panels::pages/auth/login.form.actions.authenticate.label') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page.simple>

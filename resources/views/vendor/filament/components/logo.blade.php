@php
    $isLogin = request()->route()->getName() === 'filament.admin.auth.login';
@endphp

<div>
    <img 
        src="{{ asset('images/logo.png') }}" 
        alt="Logo Jugos Jaco"
        style="{{ $isLogin ? 'height: 20rem; margin-bottom: 2rem;' : 'height: 3.5rem;' }} width: auto; object-fit: contain;"
    />
</div>

@if ($isLogin)
    <style>
        body {
            background-color: #df4c3c !important;
        }
        
        .fi-simple-card {
            background-color: white !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-radius: 1rem !important;
            padding: 2.5rem !important;
            margin-top: 0rem !important;
        }

        .fi-simple-main {
            margin-top: 2rem !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }
    </style>
@else
    <style>
        .fi-main {
            background-color: #df4c3c !important;
        }
        
        .fi-topbar, .fi-sidebar {
            background-color: white !important;
        }
        
        .fi-card {
            background-color: white !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            border-radius: 0.75rem !important;
        }

        .fi-sidebar-item-active {
            background-color: #df4c3c !important;
            color: white !important;
        }
    </style>
@endif 
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Sistem Form Izin IHBS — Pengajuan izin digital untuk pegawai">
        <title>{{ config('app.name', 'IHBS Izin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Assets -->
        @include('partials.vite', ['inputs' => ['resources/css/app.css', 'resources/js/app.js']])
    </head>
    <body style="margin:0;padding:0;">
        {{-- Animated auth background --}}
        <div class="auth-screen">
            {{-- Decorative floating circles --}}
            <div style="position:absolute;top:10%;left:5%;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,0.07);animation:float-ring 9s ease-in-out infinite;animation-delay:-3s;pointer-events:none;"></div>
            <div style="position:absolute;bottom:15%;right:8%;width:120px;height:120px;border-radius:50%;border:1px solid rgba(245,158,11,0.12);animation:float-ring 7s ease-in-out infinite;animation-delay:-1s;pointer-events:none;"></div>

            {{-- Auth Card --}}
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>

        @auth
            <x-bottom-nav />
        @endauth
        @stack('scripts')
    </body>
</html>

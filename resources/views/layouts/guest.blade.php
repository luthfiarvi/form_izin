<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Assets with robust dev/prod fallback -->
        @include('partials.vite', ['inputs' => ['resources/css/app.css', 'resources/js/app.js']])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F1F3E0]">

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-lg border border-emerald-100 border-t-4 border-brand-accent overflow-hidden sm:rounded-lg login-card-animate">
                {{ $slot }}
            </div>
        </div>
        @auth
            <x-bottom-nav />
        @endauth
        @stack('scripts')
    </body>
</html>

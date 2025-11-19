<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Assets with robust dev/prod fallback -->
    @include('partials.vite', ['inputs' => ['resources/css/app.css', 'resources/js/app.js']])
  </head>
  <body class="font-sans antialiased text-gray-900 izin-create-bg">
    <div class="min-h-screen bg-transparent">
      <div class="sticky top-0 z-40 bg-[#294755]">
        @include('layouts.navigation')

        @isset($header)
          <header class="bg-brand-dark border-b-4 border-brand-accent shadow-md">
            <div class="max-w-6xl mx-auto py-4 px-6 sm:px-8 lg:px-10">
              {{ $header }}
            </div>
          </header>
        @endisset
      </div>

      <main class="pb-24">
        {{ $slot }}
      </main>
      <x-bottom-nav />
    </div>
    @stack('scripts')
  </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Form Izin IHBS — Kelola pengajuan izin pegawai secara digital">
    <title>{{ config('app.name', 'IHBS Izin') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Assets with robust dev/prod fallback -->
    @include('partials.vite', ['inputs' => ['resources/css/app.css', 'resources/js/app.js']])
  </head>
  <body class="font-sans antialiased text-gray-900 izin-create-bg">
    <div class="min-h-screen">

      {{-- Sticky Navbar --}}
      <div class="sticky top-0 z-40" style="background:linear-gradient(135deg,#0f2d3d 0%,#1a4a5e 100%);">
        @include('layouts.navigation')

        @isset($header)
          <header style="background:linear-gradient(135deg,#0f2d3d 0%,#1a4a5e 100%);border-bottom:2px solid rgba(245,158,11,0.40);box-shadow:0 2px 16px rgba(0,0,0,0.18);">
            <div class="max-w-6xl mx-auto py-3.5 px-6 sm:px-8 lg:px-10 flex items-center gap-3">
              <div style="width:3px;height:22px;background:linear-gradient(180deg,#f59e0b,#d97706);border-radius:2px;flex-shrink:0;"></div>
              {{ $header }}
            </div>
          </header>
        @endisset
      </div>

      {{-- Page Content --}}
      <main class="pb-24">
        {{ $slot }}
      </main>

      <x-bottom-nav />
    </div>

    {{-- Global confirm modal --}}
    <script>
    (function () {
        if (window.confirmAction) return;
        let modal, msgEl, btnYes, btnCancel, pendingForm = null;

        function initModal() {
            if (modal) return true;
            modal = document.getElementById('confirm-modal');
            if (!modal) return false;
            msgEl = document.getElementById('confirm-message');
            btnYes = document.getElementById('confirm-yes');
            btnCancel = document.getElementById('confirm-cancel');
            btnYes?.addEventListener('click', function () { if (pendingForm) pendingForm.submit(); closeModal(); });
            btnCancel?.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
            return true;
        }

        function openModal(message, form) {
            if (!initModal()) return false;
            pendingForm = form;
            if (msgEl) msgEl.textContent = message || 'Apakah Anda yakin?';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return true;
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            pendingForm = null;
        }

        function handleSubmit(event, message) {
            const form = event.target.closest('form');
            event.preventDefault();
            const pesan = message || 'Akun akan dihapus permanen dan tidak bisa dibatalkan.';
            const opened = openModal(pesan, form);
            if (!opened) { if (window.confirm(pesan)) form.submit(); }
            return false;
        }

        window.confirmAction = handleSubmit;

        function bindConfirmForms() {
            document.querySelectorAll('form.confirm-form').forEach(function (form) {
                if (form.dataset.confirmBound) return;
                form.dataset.confirmBound = '1';
                form.addEventListener('submit', function (e) {
                    handleSubmit(e, form.dataset.confirm || 'Akun akan dihapus permanen dan tidak bisa dibatalkan.');
                });
            });
        }
        bindConfirmForms();
        document.addEventListener('DOMContentLoaded', bindConfirmForms);
    })();
    </script>
    @stack('scripts')
  </body>
</html>

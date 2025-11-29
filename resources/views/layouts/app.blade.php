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
          <header class="bg-brand-dark text-white border-b-4 border-brand-accent shadow-md">
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

    {{-- Script global untuk konfirmasi form (hapus/tolak) --}}
    <script>
    (function () {
        // Jika sudah ada confirmAction dari halaman lain, jangan duplikasi
        if (window.confirmAction) return;

        let modal, msgEl, btnYes, btnCancel, pendingForm = null;

        function initModal() {
            if (modal) return true;
            modal = document.getElementById('confirm-modal');
            if (!modal) return false;
            msgEl = document.getElementById('confirm-message');
            btnYes = document.getElementById('confirm-yes');
            btnCancel = document.getElementById('confirm-cancel');

            btnYes?.addEventListener('click', function () {
                if (pendingForm) pendingForm.submit();
                closeModal();
            });
            btnCancel?.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
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
            if (!opened) {
                if (window.confirm(pesan)) {
                    form.submit();
                }
            }
            return false;
        }

        // Dipakai oleh atribut onsubmit="return confirmAction(event, ...)"
        window.confirmAction = handleSubmit;

        // Otomatis ikat ke semua form yang punya kelas confirm-form
        function bindConfirmForms() {
            document.querySelectorAll('form.confirm-form').forEach(function (form) {
                if (form.dataset.confirmBound) return;
                form.dataset.confirmBound = '1';
                form.addEventListener('submit', function (e) {
                    const msg = form.dataset.confirm || 'Akun akan dihapus permanen dan tidak bisa dibatalkan.';
                    handleSubmit(e, msg);
                });
            });
        }

        // Jalankan segera dan saat DOM siap
        bindConfirmForms();
        document.addEventListener('DOMContentLoaded', bindConfirmForms);
    })();
    </script>
    @stack('scripts')
  </body>
</html>

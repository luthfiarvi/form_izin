<section class="space-y-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.destroy', [], false) }}" class="mt-4 space-y-4 max-w-xl confirm-form" data-confirm="Yakin ingin menghapus akun Anda secara permanen?">
        @csrf
        @method('delete')

        <div>
            <x-input-label for="delete_password" value="{{ __('Password') }}" />
            <x-text-input
                id="delete_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                placeholder="Masukkan password untuk konfirmasi"
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center gap-3">
            <x-danger-button type="submit">
                {{ __('Delete Account') }}
            </x-danger-button>
            <p class="text-xs text-gray-500">Anda akan otomatis logout setelah akun terhapus.</p>
        </div>
    </form>
</section>

{{-- Modal konfirmasi khusus hapus akun --}}
<div id="confirm-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-[fadeIn_0.15s_ease-out]">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M3.84 18h16.32c1.1 0 1.77-1.18 1.23-2.14L13.23 4.86c-.55-.96-1.91-.96-2.46 0L2.61 15.86c-.54.96.14 2.14 1.23 2.14Z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Yakin ingin menghapus akun?</h3>
                <p id="confirm-message" class="mt-1 text-sm text-gray-700 leading-relaxed">Akun akan dihapus permanen dan tidak bisa dibatalkan.</p>
            </div>
        </div>
        <div class="mt-5 flex flex-col sm:flex-row sm:justify-end gap-3">
            <button type="button" id="confirm-cancel" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition">Batal</button>
            <button type="button" id="confirm-yes" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition">Ya, hapus</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    let modal, msgEl, btnYes, btnCancel, pendingForm = null;

    function ensureModal() {
        if (modal) return true;
        modal = document.getElementById('confirm-modal');
        if (!modal) return false;
        msgEl = document.getElementById('confirm-message');
        btnYes = document.getElementById('confirm-yes');
        btnCancel = document.getElementById('confirm-cancel');

        btnYes.addEventListener('click', function () {
            if (pendingForm) pendingForm.submit();
            closeModal();
        });
        btnCancel.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeModal();
        });
        return true;
    }

    function openModal(message, form) {
        if (!ensureModal()) return false;
        pendingForm = form;
        msgEl.textContent = message || 'Apakah Anda yakin?';
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

    window.confirmAction = function (event, message) {
        const form = event.target.closest('form');
        event.preventDefault();
        const opened = openModal(message, form);
        if (!opened && window.confirm(message || 'Apakah Anda yakin?')) {
            form.submit();
        }
        return false;
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const msg = form.dataset.confirm || 'Apakah Anda yakin?';
                window.confirmAction(e, msg);
            });
        });
    });
})();
</script>
@endpush

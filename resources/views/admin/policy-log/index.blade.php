<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Log Kebijakan Perizinan') }}
        </h2>
    </x-slot>

    <div class="py-8 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="get" class="mb-4 flex flex-wrap gap-3 items-end text-sm">
                        <div class="flex-1 min-w-[10rem]">
                            <label class="block text-sm mb-1">Cari Pengguna</label>
                            <input type="text" name="q" value="{{ $search }}" class="border rounded p-2 w-full" placeholder="Nama atau Email">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select name="allowed" class="border rounded p-2">
                                <option value="">Semua</option>
                                <option value="yes" @selected($allowedFilter==='yes')>Diizinkan</option>
                                <option value="no" @selected($allowedFilter==='no')>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <x-primary-button>Filter</x-primary-button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left p-2">Waktu</th>
                                    <th class="text-left p-2">Pengguna</th>
                                    <th class="text-left p-2">Kebijakan</th>
                                    <th class="text-left p-2">Hasil</th>
                                    <th class="text-left p-2">Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $reasons = (array) ($log->reasons ?? []);
                                        $firstReason = $reasons[0] ?? '-';
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 whitespace-nowrap">
                                            {{ optional($log->evaluated_at)->format('Y-m-d H:i:s') ?? $log->created_at }}
                                        </td>
                                        <td class="p-2">
                                            {{ $log->user?->name ?? '-' }}
                                            @if($log->user?->email)
                                                <div class="text-[11px] text-gray-500">{{ $log->user->email }}</div>
                                            @endif
                                        </td>
                                        <td class="p-2">{{ $log->policy_key ?? '-' }}</td>
                                        <td class="p-2">
                                            @if($log->allowed)
                                                <span class="inline-flex items-center px-2 py-[1px] rounded-full text-[11px] bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Diizinkan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-[1px] rounded-full text-[11px] bg-red-100 text-red-700 border border-red-200">
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-2 text-[11px] sm:text-xs text-gray-700">
                                            {{ $firstReason }}
                                            @if(count($reasons) > 1)
                                                <details class="mt-1">
                                                    <summary class="cursor-pointer text-emerald-700">Lihat semua</summary>
                                                    <ul class="list-disc list-inside mt-1">
                                                        @foreach($reasons as $reason)
                                                            <li>{{ $reason }}</li>
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-sm text-gray-500">
                                            Belum ada log kebijakan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Modal konfirmasi umum (gaya selaras halaman lain) --}}
<div id="confirm-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 px-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-[fadeIn_0.15s_ease-out]">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M3.84 18h16.32c1.1 0 1.77-1.18 1.23-2.14L13.23 4.86c-.55-.96-1.91-.96-2.46 0L2.61 15.86c-.54.96.14 2.14 1.23 2.14Z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Yakin ingin melanjutkan?</h3>
                <p id="confirm-message" class="mt-1 text-sm text-gray-700 leading-relaxed">Tindakan ini tidak bisa dibatalkan.</p>
            </div>
        </div>
        <div class="mt-5 flex flex-col sm:flex-row sm:justify-end gap-3">
            <button type="button" id="confirm-cancel" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition">Batal</button>
            <button type="button" id="confirm-yes" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition">Ya, tetap lanjut</button>
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

    // Fungsi global untuk dipanggil di onsubmit form (class confirm-form)
    window.confirmAction = function (event, message) {
        const form = event.target.closest('form');
        event.preventDefault();
        const opened = openModal(message, form);
        if (!opened) {
            if (window.confirm(message || 'Apakah Anda yakin?')) form.submit();
        }
        return false;
    };

    // Auto-bind seluruh form dengan class confirm-form
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

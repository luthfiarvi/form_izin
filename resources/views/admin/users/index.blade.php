<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-8 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('status'))
                        <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="get" class="mb-4 flex flex-wrap gap-3 items-end">
                        <div class="flex-1 min-w-[10rem]">
                            <label class="block text-sm mb-1">Cari</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="border rounded p-2 w-full" placeholder="Nama atau Email">
                        </div>
                        <div>
                            <x-primary-button>Filter</x-primary-button>
                        </div>
                    </form>

                    {{-- Tabel pengguna (responsif dengan scroll horizontal di mobile) --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-2">Nama</th>
                                    <th class="text-left p-2">Email</th>
                                    <th class="text-left p-2">Role</th>
                                    <th class="text-left p-2">Kepala Kepegawaian</th>
                                    <th class="text-left p-2">Status</th>
                                     <th class="text-left p-2">Skor Disiplin</th>
                                    <th class="text-left p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $u)
                                @php($st = strtolower($u->status ?? ''))
                                <tr class="border-b">
                                    <td class="p-2">{{ $u->name }}</td>
                                    <td class="p-2">{{ $u->email }}</td>
                                    <td class="p-2">{{ $u->role }}</td>
                                    <td class="p-2">{{ $u->is_kepala_kepegawaian ? 'Ya' : 'Tidak' }}</td>
                                    <td class="p-2">{{ $u->status ?? '-' }}</td>
                                    <td class="p-2">
                                        @php($disc = $u->discipline_status ?? [])
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold">{{ (int) ($u->discipline_score ?? 100) }}</span>
                                            @if(!empty($disc['label']))
                                                <span class="inline-flex items-center px-2 py-[1px] rounded-full text-[11px] border {{ $disc['badge_class'] ?? 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                                    {{ $disc['label'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-2 space-x-2">
                                        <a href="{{ route('admin.users.edit', ['user' => $u], false) }}" class="text-indigo-600">Edit</a>
                                        @if($st === 'pending')
                                            <form method="post" class="inline" action="{{ route('admin.users.approve', ['user' => $u], false) }}">
                                                @csrf
                                                <button class="text-green-600">Approve</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500">Disetujui</span>
                                        @endif
                                        <form method="post" class="inline confirm-form" action="{{ route('admin.users.destroy', ['user' => $u], false) }}" onsubmit="return confirmAction(event, this.dataset.confirm);" data-confirm="Hapus akun ini? Data tidak bisa dipulihkan.">
                                            @csrf
                                            @method('delete')
                                            <button class="text-gray-600">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Modal konfirmasi hapus akun --}}
<div id="confirm-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white/90 border border-red-100 rounded-2xl shadow-2xl max-w-md w-full p-6 animate-[fadeIn_0.15s_ease-out]">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M3.84 18h16.32c1.1 0 1.77-1.18 1.23-2.14L13.23 4.86c-.55-.96-1.91-.96-2.46 0L2.61 15.86c-.54.96.14 2.14 1.23 2.14Z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Hapus akun ini?</h3>
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
            if (pendingForm) {
                pendingForm.submit();
            }
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
        msgEl.textContent = message || 'Akun akan dihapus permanen dan tidak bisa dibatalkan.';
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
        const opened = openModal(message, form);
        if (!opened) {
            if (window.confirm(message || 'Akun akan dihapus permanen dan tidak bisa dibatalkan.')) {
                form.submit();
            }
        }
        return false;
    }

    window.confirmAction = handleSubmit;

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const msg = form.dataset.confirm || 'Akun akan dihapus permanen dan tidak bisa dibatalkan.';
                handleSubmit(e, msg);
            });
        });
    });
})();
</script>
@endpush

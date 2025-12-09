<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl sm:text-4xl text-white leading-tight tracking-wide">Manajemen User</h2>
    </x-slot>

    <div class="py-6 pb-24 text-base sm:text-lg leading-relaxed">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @php($msg = request()->string('msg')->toString())
            @if($msg)
                @php($class = str_contains($msg,'sukses')||str_contains($msg,'approved') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')
                <div class="mb-4 px-4 py-2 rounded {{ $class }}">{{ $msg }}</div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl sm:text-3xl font-semibold text-gray-900">Daftar Pengguna</h3>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.index', [], false) }}" class="bg-[color:var(--brand-green)] text-white px-5 py-2.5 rounded hover:bg-emerald-900 text-base sm:text-lg font-semibold">Kelola Lengkap</a>
                    <a href="{{ route('admin.users.create', [], false) }}" class="bg-[color:var(--brand-green)] text-white px-5 py-2.5 rounded hover:bg-emerald-900 text-base sm:text-lg font-semibold">Tambah Akun</a>
                </div>
            </div>

            {{-- Tampilan mobile: kartu per user tanpa scroll horizontal --}}
            <div class="sm:hidden space-y-3">
                @php($no = ($users->currentPage()-1)*$users->perPage()+1)
                @foreach($users as $u)
                    @php($st = strtolower($u->status ?? ''))
                    <div class="bg-white shadow-md rounded-xl border border-gray-200 p-3 text-xs">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-semibold text-sm truncate">{{ $u->name }}</div>
                                    <div class="text-gray-400 text-[10px] whitespace-nowrap">#{{ $no++ }}</div>
                                </div>
                                <div class="text-gray-600 mt-0.5 text-[11px] break-words">
                                    <span class="font-semibold">Email:</span>
                                    <span>{{ $u->email }}</span>
                                </div>
                            </div>
                            <div class="text-right text-[11px] space-y-0.5">
                                <div>
                                    <span class="font-semibold">Role:</span>
                                    <span class="capitalize">{{ $u->role }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Status:</span>
                                    <span class="capitalize
                                        @if(($u->status ?? '') === 'active') text-green-600
                                        @elseif(($u->status ?? '') === 'pending') text-yellow-600
                                        @elseif(($u->status ?? '') === 'blocked') text-red-600
                                        @else text-gray-600
                                        @endif">
                                        {{ $u->status ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if($st === 'pending')
                                <form action="{{ route('admin.users.approve', ['user' => $u], false) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded bg-[color:var(--brand-green)] text-white text-[11px]">Accept</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" class="confirm-form" data-confirm="Yakin mau tolak user ini?" onsubmit="return confirmAction(event, this.dataset.confirm);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-red-500 text-white text-[11px]">Reject</button>
                                </form>
                            @elseif($u->id !== auth()->id())
                                <a href="{{ route('admin.users.edit', ['user' => $u], false) }}" class="px-2 py-1 rounded bg-blue-500 text-white text-[11px]">Edit</a>
                                <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" class="confirm-form" data-confirm="Hapus akun ini? Data tidak bisa dipulihkan." onsubmit="return confirmAction(event, this.dataset.confirm);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-red-500 text-white text-[11px]">Hapus</button>
                                </form>
                            @else
                                <span class="text-gray-400 text-[11px]">Tidak bisa edit/hapus diri sendiri</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tabel penuh untuk layar >= sm --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-xl overflow-hidden text-base sm:text-lg">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">#</th>
                            <th class="py-3 px-4 text-left">Username</th>
                            <th class="py-3 px-4 text-left">Role</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($no = ($users->currentPage()-1)*$users->perPage()+1)
                        @foreach($users as $u)
                            @php($st = strtolower($u->status ?? ''))
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $no++ }}</td>
                                <td class="py-2 px-4">{{ $u->name }}</td>
                                <td class="py-2 px-4 capitalize">{{ $u->role }}</td>
                                <td class="py-2 px-4 capitalize">{{ $u->status ?? '-' }}</td>
                                <td class="py-2 px-4 space-x-2 whitespace-nowrap">
                                    @if($st === 'pending')
                                        <form action="{{ route('admin.users.approve', ['user' => $u], false) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-[color:var(--brand-green)] text-white px-4 py-2 rounded hover:bg-emerald-900 text-base">Accept</button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" class="inline confirm-form" data-confirm="Yakin mau tolak user ini?" onsubmit="return confirmAction(event, this.dataset.confirm);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-base">Reject</button>
                                        </form>
                                    @elseif($u->id !== auth()->id())
                                        <a href="{{ route('admin.users.edit', ['user' => $u], false) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-base">Edit</a>
                                        <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" class="inline confirm-form" data-confirm="Hapus akun ini? Data tidak bisa dipulihkan." onsubmit="return confirmAction(event, this.dataset.confirm);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-base">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-base">Tidak bisa edit/hapus diri sendiri</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>

    <x-bottom-nav />
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
        // Bind ulang semua form dengan kelas confirm-form agar pasti intercept submit
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

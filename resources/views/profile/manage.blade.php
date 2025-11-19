<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Manajemen User</h2>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @php($msg = request()->string('msg')->toString())
            @if($msg)
                @php($class = str_contains($msg,'sukses')||str_contains($msg,'approved') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')
                <div class="mb-4 px-4 py-2 rounded {{ $class }}">{{ $msg }}</div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Daftar Pengguna</h3>
                <a href="{{ route('admin.users.index', [], false) }}" class="bg-[color:var(--brand-green)] text-white px-4 py-2 rounded hover:bg-emerald-900">Kelola Lengkap</a>
            </div>

            {{-- Tampilan mobile: kartu per user tanpa scroll horizontal --}}
            <div class="sm:hidden space-y-3">
                @php($no = ($users->currentPage()-1)*$users->perPage()+1)
                @foreach($users as $u)
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
                            @if(($u->status ?? '') === 'pending')
                                <form action="{{ route('admin.users.approve', ['user' => $u], false) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded bg-[color:var(--brand-green)] text-white text-[11px]">Accept</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" onsubmit="return confirm('Yakin mau tolak user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 rounded bg-red-500 text-white text-[11px]">Reject</button>
                                </form>
                            @elseif($u->id !== auth()->id())
                                <a href="{{ route('admin.users.edit', ['user' => $u], false) }}" class="px-2 py-1 rounded bg-blue-500 text-white text-[11px]">Edit</a>
                                <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" onsubmit="return confirm('Yakin mau hapus user ini?')">
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
                <table class="min-w-full bg-white shadow-md rounded-xl overflow-hidden">
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
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $no++ }}</td>
                                <td class="py-2 px-4">{{ $u->name }}</td>
                                <td class="py-2 px-4 capitalize">{{ $u->role }}</td>
                                <td class="py-2 px-4 capitalize">{{ $u->status ?? '-' }}</td>
                                <td class="py-2 px-4 space-x-2 whitespace-nowrap">
                                    @if(($u->status ?? '') === 'pending')
                                        <form action="{{ route('admin.users.approve', ['user' => $u], false) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-[color:var(--brand-green)] text-white px-3 py-1 rounded hover:bg-emerald-900 text-sm">Accept</button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau tolak user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Reject</button>
                                        </form>
                                    @elseif($u->id !== auth()->id())
                                        <a href="{{ route('admin.users.edit', ['user' => $u], false) }}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Edit</a>
                                        <form action="{{ route('admin.users.destroy', ['user' => $u], false) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-sm">Tidak bisa edit/hapus diri sendiri</span>
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

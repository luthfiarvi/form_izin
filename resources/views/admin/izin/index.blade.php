<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manajemen Izin') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="get" class="mb-4 flex gap-3 items-end">
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select name="status" class="border rounded p-2">
                                <option value="">Semua</option>
                                <option value="pending" @selected(request('status')==='pending')>Pending</option>
                                <option value="approved" @selected(request('status')==='approved')>Approved</option>
                                <option value="rejected" @selected(request('status')==='rejected')>Rejected</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Cari</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="border rounded p-2" placeholder="Nama atau Email">
                        </div>
                        <div>
                            <x-primary-button>Filter</x-primary-button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-2">ID</th>
                                    <th class="text-left p-2">Pemohon</th>
                                    <th class="text-left p-2">Lampiran</th>
                                    <th class="text-left p-2">Status</th>
                                    <th class="text-left p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($forms as $f)
                                <tr class="border-b">
                                    <td class="p-2">{{ $f->id }}</td>
                                    <td class="p-2">{{ $f->user?->name }}<br><span class="text-xs text-gray-500">{{ $f->user?->email }}</span></td>
                                    <td class="p-2">
                                        @if ($f->attachment_path)
                                            @php $fn = basename($f->attachment_path); @endphp
                                            <a class="text-indigo-600" href="{{ route('files.attachment', ['filename' => $fn], false) }}" target="_blank">Lihat</a>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        @if ($f->approved_at)
                                            <span class="text-green-600">Approved</span>
                                        @elseif ($f->rejected_at)
                                            <span class="text-red-600">Rejected</span>
                                        @else
                                            <span class="text-yellow-600">Pending</span>
                                        @endif
                                    </td>
                                    <td class="p-2 space-x-2">
                                        <a href="{{ route('admin.izin.show', ['formIzin' => $f], false) }}" class="text-indigo-600">Detail</a>
                                        <form method="post" action="{{ route('admin.izin.update', ['formIzin' => $f], false) }}" class="inline">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="action" value="approve">
                                            <button class="text-green-600">Approve</button>
                                        </form>
                                        <form method="post" action="{{ route('admin.izin.update', ['formIzin' => $f], false) }}" class="inline">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="action" value="reject">
                                            <button class="text-red-600">Reject</button>
                                        </form>
                                        <form method="post" action="{{ route('admin.izin.destroy', ['formIzin' => $f], false) }}" class="inline" onsubmit="return confirm('Hapus form ini?')">
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

                    <div class="mt-4">{{ $forms->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="get" class="mb-4 flex gap-3 items-end">
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
                                    <th class="text-left p-2">Nama</th>
                                    <th class="text-left p-2">Email</th>
                                    <th class="text-left p-2">Role</th>
                                    <th class="text-left p-2">Kepala Kepegawaian</th>
                                    <th class="text-left p-2">Status</th>
                                    <th class="text-left p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $u)
                                <tr class="border-b">
                                    <td class="p-2">{{ $u->name }}</td>
                                    <td class="p-2">{{ $u->email }}</td>
                                    <td class="p-2">{{ $u->role }}</td>
                                    <td class="p-2">{{ $u->is_kepala_kepegawaian ? 'Ya' : 'Tidak' }}</td>
                                    <td class="p-2">{{ $u->status ?? '-' }}</td>
                                    <td class="p-2 space-x-2">
                                        <a href="{{ route('admin.users.edit', ['user' => $u], false) }}" class="text-indigo-600">Edit</a>
                                        <form method="post" class="inline" action="{{ route('admin.users.approve', ['user' => $u], false) }}">
                                            @csrf
                                            <button class="text-green-600">Approve</button>
                                        </form>
                                        <form method="post" class="inline" action="{{ route('admin.users.destroy', ['user' => $u], false) }}" onsubmit="return confirm('Hapus pengguna ini?')">
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
